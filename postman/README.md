# Colecciones de Postman para DIAN API

Este directorio contiene las colecciones de Postman organizadas por módulos para probar todos los endpoints de la API.

## Estructura de las Colecciones

1. **01-auth.postman_collection.json**
   - Autenticación (Login, Register, Logout)
   - Gestión de Usuarios

2. **02-company.postman_collection.json**
   - Gestión de Empresas
   - Gestión de Sucursales

3. **03-fiscal.postman_collection.json**
   - Resoluciones DIAN
   - Reglas de Impuestos
   - Numeración de Documentos

4. **04-documents.postman_collection.json**
   - Certificados Digitales
   - Plantillas
   - Documentos Comerciales
   - Logs

5. **05-products.postman_collection.json**
   - Productos
   - Categorías
   - Unidades de Medida
   - Listas de Precios
   - Inventario

6. **06-customers.postman_collection.json**
   - Clientes
   - Clasificaciones
   - Términos de Pago
   - Crédito
   - Documentos

7. **07-suppliers.postman_collection.json**
   - Proveedores
   - Documentos

8. **08-location.postman_collection.json**
   - Países
   - Departamentos
   - Ciudades

9. **09-master-tables.postman_collection.json**
   - Tipos de Documento
   - Tipos de Impuesto
   - Métodos de Pago
   - Tipos de Moneda

10. **10-payroll.postman_collection.json**
    - Documentos de Nómina
    - Empleados
    - Configuraciones

## Configuración del Ambiente

1. Crea un nuevo ambiente en Postman
2. Configura las siguientes variables:
   - `base_url`: URL base de tu API (ej: http://localhost:8000)
   - `token`: Se actualizará automáticamente al hacer login

## Uso de las Colecciones

1. Importa todas las colecciones a Postman
2. Configura el ambiente con las variables necesarias
3. Ejecuta primero el endpoint de login para obtener el token
4. El token se guardará automáticamente en las variables del ambiente
5. Usa los demás endpoints que requieren autenticación

## Notas Importantes

- Todos los endpoints que requieren autenticación incluyen el header `Authorization: Bearer {{token}}`
- Los ejemplos de request bodies incluyen datos de prueba
- Algunos endpoints requieren IDs válidos (ej: customer_id, product_id)
- Los endpoints de creación de documentos DIAN requieren certificados digitales válidos
