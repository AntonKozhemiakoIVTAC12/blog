<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Component;
use App\Models\Group;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create()
    {
        return view('auth.register', [
            'groups' => Group::all() // Все существующие группы
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'group_id' => ['nullable', 'exists:groups,id']
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($request->group_id) {
            $group = Group::find($request->group_id);
            $group->users()->attach($user);
        }
        $user->assignRole('user');

        $this->createDefaultComponentsForUser($user);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }

    /**
     * Создает дефолтные компоненты для пользователя.
     */
    protected function createDefaultComponentsForUser(User $user): void
    {
        $componentsData = [
            'gost34' => [
                ['key' => '1. Общие сведения', 'label' => '1. Общие сведения'],
                ['key' => '2. Назначение и цели создания системы', 'label' => '2. Назначение и цели создания системы'],
                ['key' => '3. Характеристика объектов автоматизации', 'label' => '3. Характеристика объектов автоматизации'],
                ['key' => '4. Требования к системе', 'label' => '4. Требования к системе'],
                ['key' => '5. Состав и содержание работ', 'label' => '5. Состав и содержание работ'],
                ['key' => '6. Порядок контроля и приемки', 'label' => '6. Порядок контроля и приемки'],
                ['key' => '7. Требования к подготовке объекта', 'label' => '7. Требования к подготовке объекта'],
                ['key' => '8. Требования к документированию', 'label' => '8. Требования к документированию'],
                ['key' => '9. Источники разработки', 'label' => '9. Источники разработки'],
            ],
            'gost19' => [
                ['key' => 'general_info', 'label' => '1. Общие сведения'],
                ['key' => 'purpose', 'label' => '2. Назначение и цели создания системы'],
                ['key' => 'objects_automation', 'label' => '3. Характеристика объектов автоматизации'],
                ['key' => 'system_requirements', 'label' => '4. Требования к системе'],
                ['key' => 'work_content', 'label' => '5. Состав и содержание работ'],
                ['key' => 'control_acceptance', 'label' => '6. Порядок контроля и приемки'],
                ['key' => 'preparation_requirements', 'label' => '7. Требования к подготовке объекта'],
                ['key' => 'documentation_requirements', 'label' => '8. Требования к документированию'],
                ['key' => 'sources', 'label' => '9. Источники разработки'],
            ],
        ];

        foreach ($componentsData as $standardKey => $components) {
            foreach ($components as $index => $component) {
                Component::create([
                    'user_id' => $user->id,
                    'standard_key' => $standardKey,
                    'key' => $component['key'],
                    'label' => $component['label'],
                    'description' => null,
                    'order' => $index + 1,
                ]);
            }
        }
    }
}
