# Plan de Desarrollo API DIAN

## Objetivo del Proyecto
Desarrollar una API REST en Laravel para la gestión de documentos electrónicos en cumplimiento con los requisitos de la DIAN en Colombia.

## FASE 1: Desarrollo de la API REST 🔄

### 1. Autenticación y Gestión de Usuarios ✅
1. Configuración inicial del proyecto Laravel
2. Implementación de autenticación con Laravel Sanctum
3. Gestión de usuarios con roles:
   - super-admin
   - company-admin
   - user

### 2. Gestión de Empresas y Sucursales ✅
1. CRUD de empresas
   - Soft deletes
   - Estados activo/inactivo
   - Configuraciones específicas

2. CRUD de sucursales
   - Manejo de sucursal principal
   - Relación con empresas
   - Estados y validaciones

### 3. Gestión de Clientes ⏳
1. CRUD de clientes
2. Validación de documentos
3. Información fiscal
4. Direcciones de entrega

### 4. Documentos Electrónicos ⏳
1. Facturas Electrónicas
2. Notas Débito
3. Notas Crédito
4. Nómina Electrónica

### 5. Datos Maestros ⏳
1. Países (ISO 3166-1)
2. Departamentos/Estados
3. Municipios/Ciudades
4. Monedas (ISO 4217)
5. Tipos de documentos de identidad
6. Tipos de organizaciones
7. Regímenes tributarios
8. Responsabilidades tributarias
9. Tipos de operación
10. Tipos de documento
11. Medios de pago
12. Unidades de medida
13. Tributos

## FASE 2: Integración con DIAN ⏳

### 1. Configuración de Servicios Web
1. Implementación de clientes SOAP/REST
2. Manejo de endpoints de prueba/producción
3. Configuración de certificados

### 2. Firma Digital
1. Implementación de firma XML
2. Validación de certificados
3. Manejo de llaves criptográficas

### 3. Generación y Validación de XML
1. Construcción de documentos XML
2. Validación contra XSD
3. Manejo de anexos técnicos

### 4. Gestión de Comunicaciones
1. Envío de documentos
2. Recepción de respuestas
3. Manejo de acuses de recibo
4. Consulta de estados

## Estado Actual del Proyecto

### Completado ✅
1. Autenticación y gestión de usuarios
2. CRUD de empresas
3. CRUD de sucursales
4. Manejo de roles y permisos

### En Progreso 🔄
1. Gestión de clientes
2. Estructura de documentos electrónicos

### Pendiente ⏳
1. Datos maestros
2. Toda la Fase 2 (Integración DIAN)

## Reglas de Negocio Implementadas

### Empresas
1. Una empresa puede tener múltiples sucursales
2. Soft delete para mantener historial
3. Control de estados activo/inactivo

### Sucursales
1. Todas las sucursales son principales por defecto
2. La primera sucursal no puede eliminarse
3. Estados vinculados a la empresa

### Usuarios
1. Sistema de roles implementado
2. Permisos basados en rol
3. Vinculación con empresa y sucursal

## Próximos Pasos
1. Implementar CRUD de clientes
2. Crear migraciones para datos maestros
3. Diseñar estructura de documentos electrónicos

## Notas Técnicas
- Laravel 10.x
- PHP 8.2
- MySQL/MariaDB
- Laravel Sanctum para autenticación
- Soft Deletes para mantener historial
