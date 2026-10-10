# Lumen Field API and catalog

Companion renderer: `petchons/Little_Worlds_Project`, model 2026100701 and
stand 2026100702. The authenticated concert-permission endpoint checks the
exact placed item, model and room. `canEdit` follows this repository's existing
owner-only policy; multiplayer separately validates active DJ leases.

Catalog-only registration (explicit installation step):

```
php artisan db:seed --class=LumenFieldCatalogSeeder
```

This seed registers two catalog definitions, rejects IDs already occupied by
other assets, and never creates a room or changes player inventory. Install
the companion `Assets` first. Its documentation describes Monica ownership
and stand placement for a new room. No automatic migration or live database
change accompanies this GitHub merge.

```
php tests/concert-permission-smoke.php
php -l database/seeders/LumenFieldCatalogSeeder.php
```

The smoke test executes the actual capability method with isolated facade
fixtures. Full Laravel route/database tests require configured dependencies
and a testing database. This focused port does not import Ben's other backend
features, recovery code, database, private configuration or credentials.
