Klipper Resource Bundle
======================

The Resource bundle is a resource management layer for doctrine. This bundle has been
designed to facilitate the creation of a Batch API for processing a list of resources<sup>1</sup>
(ex. external data loader).

However, it is entirely possible to build an API Bulk above this bundle.

It allows to easily perform actions on Doctrine using the best practices automatically according
to selected options (flush for each resource or for all resources, but also skip errors of the
invalid resources), whether for a resource or set of resources.

Features include:

- All features of [Klipper Resource](https://github.com/klipperdev/resource)
- Compiler pass to override or add a custom resource domain
- Compiler pass to add a custom converter
- Configurator for Symfony Framework Bundle

Resources
---------

- [Documentation](https://doc.klipper.dev/bundles/resource-bundle)
- [Report issues](https://github.com/klipperdev/klipper/issues)
  and [send Pull Requests](https://github.com/klipperdev/klipper/pulls)
  in the [main Klipper repository](https://github.com/klipperdev/klipper)
