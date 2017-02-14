=== Tipo de Cambio Costa Rica ===
Contributors: arielcr
Tags: colones, dolares, tipo cambio, costa rica
Donate link: http://arielorozco.com
Requires at least: 3.0.1
Tested up to: 4.7
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Obtiene el tipo de cambio del dia (colones/dolares) del BCCR en Costa Rica. 

== Description ==
Permite obtener el tipo de cambio del dia del BCCR en Costa Rica e insertarlo en cualquier archivo del template
o por medio de tags en cualquier post/page. Tambien es posible convertir un monto de colones a dolares y viceversa por medio
de funciones en el template o tambien con tags en los posts.

== Installation ==
Para instalar el plugin debe de hacer lo siguiente:

1. Subir el archivo `cr-tipo-cambio.php` al folder `/wp-content/plugins/` 
2. Activar el plugin a travez del menu \'Plugins\' en WordPress

**Utilizar en plugin en un archivo del template:**

* Obtener tipo de cambio:

`<?php wptcr_tipo_cambio('COMPRA'); ?>`
`<?php wptcr_tipo_cambio('VENTA'); ?>`

* Convertir un monto:

`<?php wptcr_convertir_colones_dolares(25000); ?>``
`<?php wptcr_convertir_dolares_colones(100); ?>``

**Utilizar el plugin en un post**

Tipo de cambio de compra: [WPTCR_TIPO_CAMBIO_COMPRA]

Tipo de cambio de venta: [WPTCR_TIPO_CAMBIO_VENTA]

Colones a Dolares: [WPTCR_CONVERTIR_COLONES_DOLARES monto=20000]

Dolares a Colones: [WPTCR_CONVERTIR_DOLARES_COLONES monto=200]
