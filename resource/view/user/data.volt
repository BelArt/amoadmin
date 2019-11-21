{% extends "layout/wide.volt" %}

{% block title %}Данные из mongoDB{% endblock %}
{% block body %}
{% if user %}
<pre>{{ user }}</pre>
{% else %}
{% endif %}
{% endblock %}