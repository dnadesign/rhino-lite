title: Fixtures and Seed Data
summary: Populate development environment with non-live data.

# Fixtures and Seed Data

To ensure all the development environments are keep consistent and developers 
have limited access to live production data, when the application is in 
development mode your database should be populated from the provided fixture 
files in `app/tests/fixtures/`. These fixture files are shared between the 
running application and the test suite to force consistency.

We use the [Populate](https://github.com/dnadesign/silverstripe-populate) 
module to load objects from those fixture files whenever developers run 
`dev/tasks/PopulateTask` on our local machines.

To setup Rhino on a clean install you can also run `dev/tasks/InstallRhino` to
setup the default pages.