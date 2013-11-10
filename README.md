Basado en el módulo original de [hans]:

http://todoprestashop.com/foro/viewtopic.php?f=15&t=5274


Adaptado por Proa IT Services (http://www.proaitservices.com)

Compatible con Prestashop 1.4 y 1.5

VERSIONES

 * v 1.3: 
  NEW: Añadide referencia interna como campo modificable
 * v 1.4: 
  NEW: Añade peso como campo modificable
 * v 1.4.1:
  BUG: Corregido bug: no importa valores no numéricos
 * v 1.5.0: 
  NEW: Añade campo "activo" como campo modificable
 * v 1.5.1: 
  BUG: No importa precios no numericos
  NEW: Actualiza campo "on_sale" (en rebajas)
 * v 2.0.0: 
  NEW: Compatible con PS 1.5
 * v 2.0.1: 
  BUG: Variable $sql2 no inicalizada
 
 
 INSTRUCCIONES

NO USEÍS EN PRODUCCIÓN SIN PREVIO BACKUPS DE LA BASE DE DATOS, OJO OS LO PODEÍS CARGAR TODO SI LO USAÍS MAL

CÓMO USAR:

Imaginaros que teneís este fichero (hay que subir un fichero llamado "update.csv" con los valores que queraís actualizar en este formato):

1:20
2:40
3:60
4:10

Suponed que la primera columna, hace referencia al id del producto (en el módulo se puede elegir si es el id del producto, la referencia, la referencia de proveedor o el código ean13), y la segunda columna puede ser: cantidad, o precio.
Con estos datos, sólo hay que decir en el módulo:

1) PORQUÉ CAMPO QUIERES BUSCAR EN LA TABLA PRODUCTOS? lo dicho: por id, por referencia, por referencia proovedor, o por el código ean13.
2) EN EL FICHERO CSV, QUE COLUMNA ES LA DEL CAMPO 1)? en nuestro caso, la columna 1
3) CUAL ES EL CARÁCTER SEPARADOR? puede ser |, : , o ; en nuestro caso :
4) QUÉ QUIERES ACTUALIZAR? Puede ser Precio o Cantidad (elegimos lo que fuera)
5) EN EL FICHERO CSV QUÉ COLUMNA ES EL CAMPO 4) (el precio o la cantidad), en nuestro caso la columna 2

Poco más, si todo está ok lo actualizará (los productos con variedades no se actualizarán)
Creo que es completo y suficiente.

pd: no se os olvide subir el fichero "update.csv" dentro de la carpeta de ese módulo
saludos

