{% extends "layout/wide.volt" %}

{% block javascripts %}

{% endblock %}

{% block title %}Добавление пользователя{% endblock %}

{% block body %}
<form method="post">
    {{ form_rows(form) }}
</form>
{% endblock %}