UPGRADE
=======

## Upgrade FROM 0.5 to 0.6

This version is compatible with Rollerworks Datagrid >=0.11.

Please see the [component upgrade](https://github.com/rollerworks/datagrid/blob/master/UPGRADE.md)
instructions for more information.

* Support for Symfony 2.7 is dropped, you need at least Symfony 3.1 now.

* The `ActionTypeExtension` handling has changed:

  * Option `parameters_field_mapping` is renamed to `parameters_mapping`
  
  * Option `redirect_parameters_field_mapping` is renamed to `redirect_parameters_mapping`
  
  * When no `parameters_field_mapping` is configured the values provided by the data-provider
    are used as-is for the route parameters, use `null` to provide no route parameters.
    
    The parameters for a redirect-route must still be set explicitly with 
    `redirect_parameters_mapping` and optionally `redirect_additional_parameters`.
    
  * The `redirect_uri` can now be a bool value. When `true` is passed the current
    uri is used (based on the RequestStack) else the `redirect_route` is used to
    determine if a redirect must be included in the action's uri.
    
    By default no redirect uri is used now.

**Note:** The twig block-naming convention has changed, see the [twig extension upgrade]
instructions for more information.

[component upgrade]: https://github.com/rollerworks/datagrid/blob/master/UPGRADE.md
[twig extension upgrade]: https://github.com/rollerworks/datagrid/blob/master/UPGRADE.md

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
