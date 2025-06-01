
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Создать новый компонент</h1>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('components.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Standard Key -->
            <div class="mb-3">
                <label for="standard_key" class="form-label">Стандарт</label>
                <select name="standard_key" id="standard_key" class="form-control" required>
                    <option value="gost34">ГОСТ 34</option>
                    <option value="gost19">ГОСТ 19</option>
                    <option value="ieee830">IEEE STD 830-1998</option>
                    <option value="iso29148">ISO/IEC/IEEE 29148-2011</option>
                </select>
            </div>

            <!-- Label -->
            <div class="mb-3">
                <label for="label" class="form-label">Название компонента</label>
                <input type="text" name="label" id="label" class="form-control" required>
            </div>

            <!-- Description -->
            <div class="mb-3">
                <label for="description" class="form-label">Описание</label>
                <textarea name="description" id="description" class="form-control"></textarea>
            </div>

            <!-- Submit Button -->
            <a href="{{ route('articles.create') }}" class="btn btn-secondary btn-lg me-3">
                <i class="fas fa-arrow-left me-2"></i>Назад
            </a>
            <button type="submit" class="btn btn-primary btn-lg">Создать компонент</button>
        </form>
    </div>
    @section('scripts')
        <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const imageInput = document.getElementById('image');
                const previewImage = document.getElementById('previewImage');
                const cropperDiv = document.getElementById('imageCropper');
                let cropper;

                imageInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();

                        reader.onload = function(event) {
                            previewImage.src = event.target.result;
                            cropperDiv.style.display = 'block';

                            if (cropper) {
                                cropper.destroy();
                            }

                            cropper = new Cropper(previewImage, {
                                aspectRatio: 1,
                                viewMode: 1,
                                autoCropArea: 0.8,
                            });
                        };

                        reader.readAsDataURL(file);
                    }
                });
            });
        </script>
    @endsection
@endsection
