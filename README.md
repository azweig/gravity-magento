# Gravity Magento 2 Module

## Overview

The Gravity module for Magento 2 provides integration between your Magento store and Gravity services. This module includes webhook functionality for products and orders, a custom payment method (PGravity), and a custom shipping method (LGravity) specifically designed for API/admin integrations.

## Features

- **Webhook Integration**: Automatically send product and order data to Gravity services
- **PGravity Payment Method**: Cash payment method for API/admin use only
- **LGravity Shipping Method**: Store pickup shipping method for API/admin use only
- **Automatic Order Status**: Orders using PGravity payment method are automatically set to "processing" status
- **Multi-store Support**: Configure different settings for different store views

## Requirements

- Magento 2.3.x or higher
- PHP 7.4 or higher

## Installation

### Manual Installation

1. Create the following directory structure in your Magento installation: `app/code/Gravity`
2. Extract the module files into this directory
3. Enable the module by running the following commands:

```bash
bin/magento module:enable Gravity_Core
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:clean
