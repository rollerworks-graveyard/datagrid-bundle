UPGRADE
=======

## Upgrade FROM 0.4 to 0.5

This version is compatible with Rollerworks Datagrid 0.8, which contains
some major BC breaks! Please see the [component upgrade](https://github.com/rollerworks/datagrid/blob/v0.8.1/UPGRADE.md)
instructions for more information.

 * Support for Symfony 2.3 is dropped, you need at least Symfony 2.7 LTS or Symfony 3

 * The extension tags are renamed to follow the changes in the component.

   * `rollerworks_datagrid.column_type` is renamed to `rollerworks_datagrid.type`
   * `rollerworks_datagrid.column_extension` is renamed to `rollerworks_datagrid.type_extension`.

 * The `alias` argument of `rollerworks_datagrid.column_extension` is renamed to `extended_type`.

 * The `alias` argument of `rollerworks_datagrid.type` is no longer required, and should be removed.
   The FQCN is used as the type name now.

 * The `rollerworks_datagrid.extension` is no longer supported, you need to register your types
   and types extensions directly as services with the correct tags.
