# Integración DIAN

Este directorio contiene toda la integración con los servicios de la DIAN para facturación electrónica.

## Estructura

```
Dian/
├── Config/                 # Configuraciones y constantes
├── Contracts/             # Interfaces
├── Documents/             # Clases para cada tipo de documento
├── Exceptions/            # Excepciones personalizadas
├── Helpers/              # Funciones auxiliares
├── Services/             # Servicios principales
├── Traits/               # Traits reutilizables
├── Utils/               # Utilidades generales
└── WebServices/         # Clientes para servicios web DIAN
```

## Componentes Principales

1. **Config/**
   - Constantes y configuraciones DIAN
   - Rutas de servicios web
   - Tipos de documentos

2. **Documents/**
   - Invoice (Factura)
   - CreditNote (Nota Crédito)
   - DebitNote (Nota Débito)
   - ApplicationResponse

3. **Services/**
   - XmlBuilderService
   - SignatureService
   - ValidationService
   - QrService

4. **WebServices/**
   - SendBillService
   - GetStatusService
   - NumberingRangeService

## Uso

La integración se puede usar a través de la fachada principal `DianService`.
