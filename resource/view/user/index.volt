{% extends "layout/wide.volt" %}

{% block title %}Пользователи{% endblock %}
{% block body %}
<div class="row">
    <div class="col-md-1">
        <a href="{{ url('/user/edit') }}">
            <button type="button" class="btn btn-primary">Добавить пользователя</button>
        </a>
    </div>
</div>
    {% if users %}
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>БД</th>
                    <th>Логин</th>
                    <th>Хэш</th>
                    <th>Мыло</th>
                    <th>Debug</th>
                    <th>Дата создания</th>
                    <th>Config</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                {% for i in 0..(users | length - 1) %}
                    <tr>
                        <td>
                            <a href="{{ url('/user/data/'~users[i]._id) }}">Показать</a>
                        </td>
                        <td>{{ users[i].name }}</td>
                        <td><span>{{ users[i].hash }}</span></td>
                        <td>{{ users[i].email }}</td>
                        {% if users[i].debug is defined %}
                        <td>Да</td>
                        {% else %}
                        <td>Нет</td>
                        {% endif %}
                        {% if users[i].createdAt is empty %}
                        <td></td>
                        {% else %}
                        <td>{{ date('d.m.Y H:i:s',users[i].createdAt) }}</td>
                        {% endif %}
                        <td>
                            <a href="{{ url('/user/config/'~users[i]._id) }}"><i class="glyphicon glyphicon-list-alt"></i></a>
                        </td>
                        <td>
                            <a href="{{ url('/user/edit/'~users[i]._id) }}"><i class="glyphicon glyphicon-pencil"></i></a>
                        </td>
                    </tr>
                {% endfor %}
            </tbody>
        </table>
    {% else %}
    {% endif %}
{% endblock %}