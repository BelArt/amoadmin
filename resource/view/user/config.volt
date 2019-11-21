{% extends "layout/wide.volt" %}

{% block javascripts %}

{% endblock %}

{% block title %}Редактирование config{% endblock %}

{% block body %}
<form action="" method="post">
<table class="table table-striped">
    <thead>
        <tr>
            <th>Название config</th>
            <th>Значение</th>
        </tr>
    </thead>
    <tbody>
{% if configs != null %}
    {% for config in configs %}
        <tr>
            <td>{{config['config_name']}}</td>
            <td>
                <input type="text" name="{{config['config_name']}}" class="form-control" value="{{config['config_value']}}">
            </td>
        </tr>
    {% endfor %}
{% else %}
        <tr>
            <td>Конфиг пуст</td>
            <td></td>
        </tr>
{% endif %}
    {% if debug %}
        <tr>
            <td>Debug</td>
            <td><input type="text" name="debug" class="form-control" value=""></td>
        </tr>
    {% endif %}
    {% if logLevel %}
        <tr>
            <td>Log level</td>
            <td><input type="text" name="log_level" class="form-control" value=""></td>
        </tr>
    {% endif %}
    </tbody>
</table>
    <button type="submit" class="btn btn-primary">Сохранить</button>
</form>
{% endblock %}