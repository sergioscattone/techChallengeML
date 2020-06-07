# MERCADO LIBRE TECH CHALLENGE

El siguiente es un desafío de tecnología realizado para una entrevisa laboral de Mercado Libre.<br>
El requerimiento del desafío se encuentra [aquí](tech_challenge.pdf).

Para levantar este entorno hay que realizar los siguientes pasos:
- docker-compose up
- mv .env.example a .env
- chmod -R 777 storage/
- docker exec apiMLContainer "php artisan migrate && php artisan db:seed"
- docker exec apiMLContainer "php artisan test" (opcional unit tests)

<br>El servicio por default se levantara en el puerto 8081 => http://localhost:8081/api/


# SERVICIOS EXPUESTOS

Para llamar a cualquier servicio es preciso enviar en el header o body del request con nombre de parametro <b>token</b>, un token de validación que es el <b>APP_KEY</b> de aplicación de Laravel.<br>
Este se encuentra en el archivo .env, recordar remover el <b>base64:/</b> del token.
<br>Por ejempo si la APP_KEY es "<i>base64:/abc1234</i>" el token es únicamente <i><b>abc1234</b></i>.

En todos los servicios GET se devolvera la colección entera (filtrada según el caso) cuando no se especifique el ID.

## GET api/charges/{charge_id}

<u>Parametros opcionales:</u><br>
<b>from:</b> fecha desde<br>
<b>to:</b> fecha hasta<br>
<b>invoice_id:</b> ID de la factura<br>
<b>user_id:</b> ID del usuario<br>
<b>event_id:</b> ID del evento

<u>Request de ejemplo:</u>
<pre style="background-color:#FEE7E2">
{
  "token": APP_KEY,
  "from": "2020-05-01 00:00:00",
  "to": "2020-07-01 08:00:00",
  "invoice_id": 21,
  "user_id": 1
}
</pre>

<u>Response de ejemplo:</u>
<pre style="background-color:#F2ECEA">
[{
    "id":43
    "user_id":1
    "amount":"65512.30"
    "debt_amount":"0.00"
    "invoice_id":21
    "date":"2020-06-28 04:30:34"
  },
  {
    "id":52,
    "user_id":1,
    "amount":"14830.90",
    "debt_amount":"0.00",
    "invoice_id":21,
    "date":"2020-06-02 18:38:44"
}]
</pre>

<hr>

## GET api/events/{event_id}

<u>Parametros opcionales:</u><br>
<b>from:</b> fecha desde<br>
<b>to:</b> fecha hasta

<u>Request de ejemplo:</u>
<pre style="background-color:#FEE7E2">
{
  "token": APP_KEY,
  "from": "2020-05-01 00:00:00",
  "to": "2020-07-01 08:00:00",
}
</pre>

<u>Response de ejemplo:</u>
<pre style="background-color:#F2ECEA">
[{
    "id":1,
    "user_id":7,
    "currency":"USD",
    "type":"SERVICIOS",
    "subtype":"FIDELIDAD",
    "amount":"345.94",
    "date":"2020-06-11 01:58:41"
  },{
    "id":2,
    "user_id":10,
    "currency":"ARS",
    "type":"SERVICIOS",
    "subtype":"CR\u00c9DITO",
    "amount":"195.37",
    "date":"2020-06-20 03:04:08"
}]
</pre>

<hr>

## GET api/invoices/{invoice_id}

<u>Parametros opcionales:</u><br>
<b>from:</b> fecha desde<br>
<b>to:</b> fecha hasta<br>
<b>user_id:</b> ID del usuario

<u>Request de ejemplo:</u>
<pre style="background-color:#FEE7E2">
{
  "token": APP_KEY,
  "from": "2020-05-01 00:00:00",
  "to": "2020-07-01 08:00:00",
  "user_id": 1
}
</pre>

<u>Response de ejemplo:</u>
<pre style="background-color:#F2ECEA">
[{
    "id":11,
    "user_id":1,
    "period":"2020-05",
    "amount":"172753.26",
    "debt_amount":"0.00"
  },{
    "id":12,
    "user_id":1,
    "period":"2020-06",
    "amount":"151155.72",
    "debt_amount":"98475.57"
}}
</pre>

<hr>

## GET api/payments/{payment_id}

<u>Parametros opcionales:</u><br>
<b>from:</b> fecha desde<br>
<b>to:</b> fecha hasta<br>
<b>user_id:</b> ID del usuario

<u>Request de ejemplo:</u>
<pre style="background-color:#FEE7E2">
{
  "token": APP_KEY,
  "from": "2020-05-01 00:00:00",
  "to": "2020-07-01 08:00:00",
  "user_id": 1
}
</pre>

<u>Response de ejemplo:</u>
<pre style="background-color:#F2ECEA">
[{
    "id":9,
    "user_id":1,
    "amount":"2181.98",
    "date":"2020-06-07 15:54:13"
  },{
    "id":10,
    "user_id":1,
    "amount":"2513.92",
    "date":"2020-06-07 15:54:13"
}}
</pre>

<hr>

## GET users/status/{status_id}

<u>Parametros opcionales:</u><br>
<b>from:</b> fecha desde<br>
<b>to:</b> fecha hasta

<u>Request de ejemplo:</u>
<pre style="background-color:#FEE7E2">
{
  "token": APP_KEY,
  "from": "2020-05-01 00:00:00",
  "to": "2020-07-01 08:00:00"
}
</pre>

<u>Response de ejemplo:</u>
<pre style="background-color:#F2ECEA">
[{
    "id":1,
    "user_id":7,
    "debt_amount":"151704.52",
    "last_update":"2020-06-07 17:18:56"
  },
  {
    "id":2,
    "user_id":10,
    "debt_amount":"19435.62",
    "last_update":"2020-06-07 17:18:56"
}]
</pre>

<hr>

## GET users/{user_id}/status

<u>Response de ejemplo:</u>
<pre style="background-color:#F2ECEA">
{
  "id":10,
  "user_id":1,
  "debt_amount":"299633.55",
  "last_update":"2020-06-07 17:24:08"
}
</pre>

<hr>

## POST api/event

<u>Parametros obligatorios:</u><br>
<b>amount:</b> monto<br>
<b>user_id:</b> ID del usuario<br>
<b>currency_id:</b> ID de la moneda<br>
<b>type_id:</b> ID del [concepto](tech_challenge.pdf)

<u>Request de ejemplo:</u>
<pre style="background-color:#FEE7E2">
{
  "token": APP_KEY,
  "amount": 54,
  "user_id": 4,
  "currency_id": 1,
  "type_id": 6
}
</pre>

<u>Response de ejemplo:</u>
<pre style="background-color:#F2ECEA">
{
  "id": 366,
  "user_id": 4,
  "currency": "ARS",
  "type": "SERVICIOS",
  "subtype": "PUBLICIDAD",
  "amount": 54,
  "date": "2020-06-07 17:59:40"
}
</pre>

<hr>

## POST api/payment

<u>Parametros obligatorios:</u><br>
<b>amount:</b> monto<br>
<b>user_id:</b> ID del usuario

<u>Request de ejemplo:</u>
<pre style="background-color:#FEE7E2">
{
  "token": APP_KEY,
  "amount": 54,
  "user_id": 4
}
</pre>

<u>Response de ejemplo:</u>
<pre style="background-color:#F2ECEA">
{
  "id": 2328,
  "user_id": 4,
  "amount": 54,
  "date": "2020-06-07 18:04:26"
}
</pre>

# SOLUCIÓN

Se expondra las desiciones de diseño y arquitectura elegidas organizadas por categorías.

## Requerimientos
- <u>Los posts de pagos puede recibir miles de request por minuto:</u> se implemento la solución con nginx para reducir los costes de recursos
- <u>Tiempos de respuesta lo más bajo posibles:</u> se utiliza un entorno de redis para catchear la información devuelta por el sistema
- <u>Los datos se deben persistir en una base de datos:</u> Se persiste la información en un entorno mysql independiente levantado desde docker

## Deploy / Environment
Se desarrollo 3 dockers que levantan al correr <i>docker-compose up</i>.<br>
Estos 3 dockers son la aplicación desarrollada sobre laravel, otro con el entorno de mysql y otro con un entorno de redis.<br>
Luego de levantar el entorno es importante correr los comando para generar la estructura de datos y cargarla con información de prueba con el comando:
- docker exec apiMLContainer "php artisan migrate && php artisan db:seed" 

## Modelos / Entidades
Se desarrollo sobre la capa de modelos de laravel <i>app\http\models</i>.<br>
Se generaron las respectivas relaciones para administrar las entidades como objetos.

## Servicios
Aquí se consolido la lógica de las operaciones de negocio, y también las operaciones sobre los modelos; por lo acotada de la solución se decidio no realizar una capa de repositorios y administrar los modelos directamente desde la capa de servicio.

## Controllers
No tiene lógica de los procesos, por ende no se realizaron unit test especificos para ellos.

## Exposición / Middleware
Todos los servicios expuestos están en <i>route\api.php</i> y atraviesan 3 middlewares.<br>
Los middlewares se encuentran en <i>app\Http\Middleware</i> y son:
- <u>Authorization:</u> Valida que se envíe un <b>token</b> en la cabecera o cuerpo de mensaje con la <b>APP_KEY</b> de laravel, que se encuentra en <b>.env</b>
- <u>CacheGetters:</u> Debido al requerimiento de velocidad en la devolución de información se implemento un servicio <b>redis</b> que cachea la información según la solicitud que envío el usuario. El middleware parsea la solicitud enviada por el cliente y registra la respuesta en cache para ofrecerla cuando se vuelva a solicitar.</b>
- <u>ClearCache:</u> Limpia la información cacheada, esto se usa en los casos de solicitudes HTTP POST. Se entiende que luego de alguna solicitud POST la informaión de nuestro sistema se actualizo y no podemos seguir devolviendo la que se encontraba catcheada.

## Test
Se realizaron 4 niveles de tests para verificar el correcto funcionamiento de las operaciones y garantizar que los cambios futuros en los servicios disparen fallos en dichos tests.<br>
Los suites de test se encuentran en la carpeta <i>test</i> y son:
- <u>Unitarios:</u> Aquí se testean todos los servicios y cada uno de sus métodos
- <u>Endpoints:</u> Aquí se testean el acceso (y denegación en caso de no enviar el token) de cada uno de los servicios expuestos y sus variaciones
- <u>Consistencia:</u> Aquí se testean que la información retribuida y operaciones realizadas por los servicios sea y haga exactamente lo que se espera
- <u>Integración:</u> Aquí se testea un caso completo de un nuevo usuario al que se le cargan 5 eventos; luego se realizan 2 pagos; luego se intenta pagar por encima del límite de su deuda corroborando el error correspondiente; y luego se cancela la deuda total del usuario.
Al final se corrobora que el estado financiero del usuario sea de deuda cero

## Configuración
El archvio <b>.env</b> contiene la configuración de base de datos y redis (además del resto de configuraciones de laravel) y en el archivo <b>phpunit.xml</b> se encuentra la información relativa a los tests.

# LICENCE
[MIT license](https://opensource.org/licenses/MIT).
