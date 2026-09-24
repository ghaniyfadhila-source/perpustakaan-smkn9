#!/bin/bash
set -e

# Disable conflicting MPMs
a2dismod mpm_event 2>/dev/null || true
a2dismod mpm_worker 2>/dev/null || true
a2dismod mpm_prefork 2>/dev/null || true

# Enable the correct one for PHP
a2enmod mpm_prefork 2>/dev/null || true

# Execute the main process
exec apache2-foreground
