<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
      

    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('css/create-todo.css') }}">

</head>
<body>
    <div class="container">
        <h1>Créer une nouvelle tâche</h1>

        <form action="{{ route('todos.store') }}" method="POST">
            @csrf

            
            <div class="form-group">
                <label for="title">Name</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required>
                @error('title')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
        <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description">{{ old('description') }}</textarea>
                @error('description')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group"> 
                <label for="user_id">Assign to User</label>
                <select id="user_id" name="user_id" required> 
                    <option value="">Select a user</option>
                    @foreach($users as $user) 
                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}> {{ $user->name }} </option> 
                    @endforeach 
                </select>
                @error('user_id') 
                <div class="error">{{ $message }}</div> 
                @enderror 
            </div>
            
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">Create Todo</button>
                <a href="{{ route('todos.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>

        <a href="{{ route('todos.index') }}">← Retour à la liste des tâches</a>
    </div>
</body>
</html>