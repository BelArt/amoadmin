{% extends "layout/main.volt" %}

{% block menu %}
	{% include 'custom/menu' with [
		'menu_left': [
			['name':'Главная', 'url': '/'],
			['name':'Пользователи', 'url': '/user/index']
		],
		'menu_right': [
			['url': '/auth/logout', 'name':'Выйти']
		]
	] %}
{% endblock %}

{% block top %}<div class="container">{% endblock %}
{% block bottom %}</div>{% endblock %}