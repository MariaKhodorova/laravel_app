@extends('layouts.app')

@section('title', 'Список данных')

@section('content')
    <h1>Все отправленные данные</h1>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Имя</th>
                <th>Email</th>
                <th>Сообщение</th>
            </tr>
        </thead>
        <tbody>
            @foreach($submissions as $submission)
                <tr>
                    <td>{{ $submission['name'] }}</td>
                    <td>{{ $submission['email'] }}</td>
                    <td>{{ $submission['message'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
