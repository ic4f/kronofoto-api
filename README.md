# kronofoto-api

**kronofoto-api** is a read-only REST API for the [Foterpan Iowa digital archive](http://fortepan.us). 
It is part of *Kronofoto*, an umbrella term for three loosely coupled applications built to host and
manage a digital archive like Fortepan Iowa:

* kronofoto-api (API consumed by a web-based front-end) 
* [kronofoto-ng](https://github.com/ic4f/kronofoto-ng) (public front-end)
* kronofoto-admin (web-based archive management system)

kronofoto-api is implemented on top of the [Slim framework](https://github.com/slimphp/Slim). It
uses [Doctrine DBAL](https://github.com/doctrine/dbal) as its (relational) database abstraction
layer and [Codeception](https://github.com/Codeception/Codeception) as its testing framework.

### Expected Release 
Currently, kronofoto-api is in pre-alpha state; however, it is operational and is
used by the development version of kronofoto-ng ([current demo version available
here](http://sergey.cs.uni.edu:8080/public/collections)). I expect to have it ready for an alpha
release this summer.

### Extension 
Although built for Fortepan Iowa, kronofoto-api is designed to be collection-agnostic and
can be adapted to similar collections. The underlying data model is built around donors, items, item
collections, item metadata, and static pages. Certainly, it can be extended to include other
entities. 

The API is intended to be consumed exclusively by a web-based front-end. It is not built to be
public-facing.

## License 
[MIT](LICENSE)
