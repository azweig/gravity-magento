# Gravity Magento 2 Module

## Descripción

El módulo Gravity para Magento 2 proporciona integración entre tu tienda Magento y los servicios de Gravity. Este módulo incluye funcionalidad de webhooks para productos y pedidos, un método de pago personalizado (PGravity) y un método de envío personalizado (LGravity) específicamente diseñados para integraciones API/admin.

## Características

- **Integración de Webhooks**: Envía automáticamente datos de productos y pedidos a los servicios de Gravity
- **Método de Pago PGravity**: Método de pago en efectivo solo para uso API/admin
- **Método de Envío LGravity**: Método de recogida en tienda solo para uso API/admin
- **Estado de Pedido Automático**: Los pedidos que utilizan el método de pago PGravity se establecen automáticamente en estado "processing"
- **Soporte Multi-tienda**: Configura diferentes ajustes para diferentes vistas de tienda


## Requisitos

- Magento 2.3.x o superior
- PHP 7.4 o superior


## Guía de Instalación Paso a Paso

### Opción 1: Instalación Manual

1. **Descargar el código**

```shellscript
git clone https://github.com/azweig/gravity-magento.git
```


2. **Crear la estructura de directorios en Magento**

```shellscript
mkdir -p app/code/Gravity
```


3. **Copiar los archivos del módulo**

```shellscript
# Navega a la carpeta donde clonaste el repositorio
cd gravity-magento

# Copia solo la carpeta Gravity a la ubicación correcta de Magento
cp -r Gravity/* /ruta/a/tu/magento/app/code/Gravity/
```


4. **Verificar la estructura de archivos**

Asegúrate de que la estructura de archivos sea la siguiente:

```plaintext
app/code/Gravity/
├── Api/
├── Helper/
├── Logger/
├── Model/
├── Observer/
├── Plugin/
├── etc/
├── composer.json
├── registration.php
└── README.md
```


5. **Habilitar el módulo**

```shellscript
bin/magento module:enable Gravity_Core
bin/magento setup:upgrade
```


6. **Compilar el código**

```shellscript
bin/magento setup:di:compile
```


7. **Limpiar la caché**

```shellscript
bin/magento cache:clean
bin/magento cache:flush
```


8. **Verificar la instalación**

```shellscript
bin/magento module:status Gravity_Core
```

Deberías ver "Gravity_Core" en la lista de módulos habilitados.




### Opción 2: Instalación con Composer

1. **Añadir el repositorio a tu `composer.json`**

```shellscript
composer config repositories.gravity vcs https://github.com/azweig/gravity-magento.git
```


2. **Requerir el módulo**

```shellscript
composer require gravity/module-core:dev-main
```


3. **Habilitar el módulo**

```shellscript
bin/magento module:enable Gravity_Core
bin/magento setup:upgrade
```


4. **Compilar el código**

```shellscript
bin/magento setup:di:compile
```


5. **Limpiar la caché**

```shellscript
bin/magento cache:clean
bin/magento cache:flush
```




## Configuración

### Configuración General

1. Inicia sesión en el panel de administración de Magento
2. Ve a **Tiendas > Configuración > Gravity > Gravity Core**
3. Configura los siguientes ajustes:

1. **Habilitar**: Activa o desactiva el módulo
2. **ID de Organización**: Tu ID de Organización de Gravity
3. **ID de Cliente**: Tu ID de Cliente de Gravity
4. **Secreto de Cliente**: Tu Secreto de Cliente de Gravity
5. **URL de WebHooks para Productos**: URL para webhooks de productos
6. **URL de WebHooks para Pedidos**: URL para webhooks de pedidos
7. **URL de Token**: URL para autenticación de token (opcional)
8. **Modo de Depuración**: Activa o desactiva el registro





### Métodos de Pago y Envío

1. Ve a **Tiendas > Configuración > Gravity > Gravity Core > Métodos de Pago y Envío**
2. Configura los siguientes ajustes:

1. **Habilitar Método de Pago PGravity**: Activa o desactiva el método de pago PGravity
2. **Habilitar Método de Envío LGravity**: Activa o desactiva el método de envío LGravity





## Uso en Integraciones

### Creación de Pedidos con API

Cuando crees pedidos a través de API o admin, puedes usar el método de pago PGravity y el método de envío LGravity:

```php
// Establecer método de pago
$order->setPaymentMethod('pgravity');

// Establecer método de envío
$order->setShippingMethod('lgravity_lgravity');

// El estado del pedido se establecerá automáticamente en "processing" al guardar
$order->save();
```

### Ejemplo de API REST

```plaintext
POST /V1/orders
{
    "entity": {
        "customer_email": "cliente@ejemplo.com",
        "payment": {
            "method": "pgravity"
        },
        "shipping_method": "lgravity_lgravity"
    }
}
```

## Funcionalidad de Webhooks

El módulo envía automáticamente notificaciones webhook a los servicios de Gravity para los siguientes eventos:

- Creación de nuevos productos
- Actualizaciones de productos
- Eliminaciones de productos
- Actualizaciones de cantidad de stock
- Creación de nuevos pedidos
- Actualizaciones de pedidos


### Formato de Datos de Webhook

Los webhooks se envían en formato JSON con la siguiente estructura:

```json
{
    "data": {
        // Datos de la entidad (producto o pedido)
    },
    "eventType": "TipoDeEvento"
}
```

Los tipos de eventos incluyen:

- ProductCreated
- ProductUpdated
- ProductDeleted
- ProductSkuStockUpdated
- OrderCreated
- OrderUpdated


## Depuración

Los logs se almacenan en `var/log/gravity.log` cuando el modo de depuración está habilitado.

## Solución de Problemas Comunes

### El módulo no aparece en la configuración

- Verifica que el módulo esté habilitado: `bin/magento module:status Gravity_Core`
- Limpia la caché: `bin/magento cache:clean`
- Comprueba los logs en `var/log/exception.log` y `var/log/system.log`


### Los webhooks no se envían

- Verifica que el módulo esté habilitado en la configuración
- Comprueba que las URLs de webhook estén correctamente configuradas
- Activa el modo de depuración y revisa `var/log/gravity.log`
- Verifica las credenciales de API (ID de Organización, ID de Cliente, Secreto de Cliente)


### Los métodos de pago/envío no aparecen

- Estos métodos están diseñados para ser utilizados solo a través de API/admin, no aparecerán en el checkout del frontend
- Verifica que estén habilitados en la configuración del módulo


## Soporte

Para soporte, por favor contacta:

- Email: [support@gravity.com](mailto:support@gravity.com)
- Sitio web: [https://www.gravity.com](https://www.gravity.com)


## Licencia

Este módulo está licenciado bajo licencia propietaria. Consulta el archivo LICENSE para más detalles.
