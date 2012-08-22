# Arden-PHP

[![Build Status](https://secure.travis-ci.org/zombor/Arden-PHP.png)](http://travis-ci.org/zombor/Arden-PHP)

Arden is a repository pattern library for php. It is a counterpart for [Arden](https://github.com/zombor/arden), which is a repository pattern gem for ruby.

You can use Arden to abstract and isolate your persistance logic in your application. It is recommended to use Arden with POPOs (Plain Old PHP Objects) as your data entities, but you could also use your own or some other base class.

Using POPOs for your data entities lets you design a better system, keeping your business logic away from your database. This will help you with testing since you will have a decoupled system.

## Usage

You can use Arden with any PSR-0 compatible framework, or you can simply load the classes yourself.

You would generally have a repository class for each of your database tables. Simply extend a base Repository class as desribed in the below section.

## Repository Drivers

There are currently the following repository drivers:

 - Kohana Framework

### Kohana

The Kohana driver uses the built in kohana database builder module. Just extend `Arden_Repository_Database`.

## Contributing

Contribution is welcome. Make sure you know about phpspec, BDD and all that jazz. When in doubt, ask about contributing.

## Running Specs

Install composer, then run `composer install`.

Then run `./phpspec-composer.php specs -f d -b -c`
