# Gravity_Core Magento 2 Module

## Descripción
El módulo **Gravity_Core** para Magento 2 integra tu tienda con los servicios de Gravity, permitiendo:

- **Webhooks:** Notificaciones automáticas para eventos de productos y pedidos (creación, actualización, eliminación, actualización de stock).
- **Método de Pago PGravity:** Método de pago en efectivo para integraciones API/admin.
- **Método de Envío LGravity:** Método de envío (recogida en tienda) para integraciones API/admin.
- **Actualización Automática de Estado:** Los pedidos que usan PGravity se establecen automáticamente en el estado "processing".
- **Soporte Multi-tienda:** Configuración independiente para diferentes vistas de tienda.

## Requisitos
- **Magento:** 2.3.x o superior.
- **PHP:** 7.4 o superior.
- **Permisos:** Asegúrate de que los directorios críticos (`var`, `pub/static`, `generated`) tengan permisos de escritura.
- **Entorno:** Adaptado para instalaciones Bitnami u otros entornos Linux.

## Instalación

### Opción 1: Instalación Manual

1. **Clonar el repositorio en el directorio adecuado**  
   Navega al directorio `app/code` de tu instalación de Magento y clona el repositorio:  
   `cd /bitnami/magento/app/code`  
   `sudo git clone https://github.com/azweig/gravity-magento.git`

2. **Crear la estructura de directorios**  
   Magento requiere que el módulo se ubique en `app/code/Vendor/Module`. Para este módulo, crea:  
   `sudo mkdir -p /bitnami/magento/app/code/Gravity/Core`

3. **Copiar el contenido del módulo**  
   Copia todos los archivos del repositorio al directorio creado:  
   `sudo cp -r /bitnami/magento/app/code/gravity-magento/* /bitnami/magento/app/code/Gravity/Core/`

4. **Actualizar archivos de registro y configuración**  
   - **registration.php** (en `app/code/Gravity/Core/registration.php`):
     ```php
     <?php
     use Magento\Framework\Component\ComponentRegistrar;

     ComponentRegistrar::register(
         ComponentRegistrar::MODULE,
         'Gravity_Core',
         __DIR__
     );
     ```
   - **module.xml** (en `app/code/Gravity/Core/etc/module.xml`):
     ```xml
     <?xml version="1.0"?>
     <config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
             xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
         <module name="Gravity_Core" setup_version="1.0.0"/>
     </config>
     ```

5. **Habilitar e instalar el módulo**  
   Desde la raíz de Magento (`/bitnami/magento`), ejecuta:  
   `sudo bin/magento module:enable Gravity_Core`  
   `sudo bin/magento setup:upgrade`  
   `sudo bin/magento setup:di:compile`  
   `sudo bin/magento cache:clean`  
   `sudo bin/magento cache:flush`

6. **Desplegar contenido estático (si es necesario)**  
   Si observas errores relacionados con archivos estáticos:  
   `sudo bin/magento setup:static-content:deploy -f`

### Opción 2: Instalación vía Composer

1. **Agregar el repositorio a Composer**  
   Desde la raíz de Magento:  
   `composer config repositories.gravity vcs https://github.com/azweig/gravity-magento.git`

2. **Instalar el módulo**  
   `composer require gravity/module-core:dev-main`

3. **Habilitar e instalar el módulo**  
   Desde la raíz de Magento:  
   `sudo bin/magento module:enable Gravity_Core`  
   `sudo bin/magento setup:upgrade`  
   `sudo bin/magento setup:di:compile`  
   `sudo bin/magento cache:clean`  
   `sudo bin/magento cache:flush`

## Configuración

1. **Acceso al Panel de Administración**  
   - Inicia sesión en el backend de Magento.
   - Navega a **Stores > Configuration > Gravity > Gravity Core**.

2. **Configuración de Opciones**  
   - **General:**
     - Habilitar o deshabilitar el módulo.
     - Configurar **ID de Organización**, **ID de Cliente** y **Secreto de Cliente**.
     - Definir las URLs para los Webhooks de Productos y Pedidos.
   - **Métodos de Pago y Envío:**
     - Habilitar **PGravity** y **LGravity** (estos métodos se usan solo vía API/admin).

3. **Guardar la configuración y limpiar la caché**  
   Cada cambio en la configuración puede requerir limpiar la caché de Magento para aplicarse:
   `sudo bin/magento cache:clean && sudo bin/magento cache:flush`

## Permisos y Entorno (Bitnami)

En entornos Bitnami, es crucial ajustar los permisos para evitar errores de escritura en los directorios. Por ejemplo:

Estos comandos aseguran que Magento pueda escribir en los directorios necesarios (cache, logs, contenido estático, etc.).

## FAQ

- **¿Por qué recibo "Unknown module(s): 'Gravity_Core'"?**  
  Verifica que el nombre del módulo en `registration.php` y `module.xml` sea **Gravity_Core** y que la estructura de directorios sea `app/code/Gravity/Core`.

- **¿Cómo soluciono errores de permisos en `var/cache`?**  
  Ajusta la propiedad y permisos del directorio `var` (y otros críticos) con:  
  `sudo chown -R bitnami:daemon /bitnami/magento/var`  
  `sudo chmod -R 775 /bitnami/magento/var`

- **¿Qué hacer si aparecen errores de archivos estáticos (404, js-translation.json, etc.)?**  
  Despliega el contenido estático:  
  `sudo bin/magento setup:static-content:deploy -f`  
  `sudo bin/magento cache:flush`

- **¿Por qué los métodos de pago/envío no aparecen en el checkout?**  
  Estos métodos están diseñados para integraciones API/admin, por lo que no se muestran en el frontend.

- **¿Cómo activo el modo developer para ver errores detallados?**  
  `sudo bin/magento deploy:mode:set developer`  
  O, de forma temporal, activa la visualización de errores en `pub/index.php`.

## Troubleshooting

- **Error de permisos:**  
  Si Magento no puede escribir en `var/cache` u otros directorios, ajusta los permisos y la propiedad con los comandos indicados anteriormente.

- **Errores durante la compilación:**  
  Ejecuta `sudo bin/magento setup:di:compile` y revisa la salida para identificar y solucionar problemas.

- **Problemas con contenido estático:**  
  Si los archivos estáticos no se generan correctamente, usa el comando de despliegue de contenido estático y limpia la caché.

- **Errores al habilitar el módulo:**  
  Verifica que la estructura de directorios y los nombres en `registration.php` y `module.xml` sean correctos.

## Soporte

Para soporte adicional, por favor abre un [issue](https://github.com/tu_usuario/tu_repositorio/issues) en este repositorio o contacta al equipo de desarrollo.

## Licencia

Este módulo se distribuye bajo licencia comercial. Consulta el archivo LICENSE para más detalles.

## Contribuciones

¡Las contribuciones son bienvenidas! Si deseas contribuir, por favor abre un pull request o un issue en este repositorio.
