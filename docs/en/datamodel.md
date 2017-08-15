title: Data Model
summary: A description of all the core objects in the application and how they relate.

# Data Model

This documentation duplicates and extends the PHPDoc headers on each of the 
classes. For a more technical documentation read each of the classes.

<div class="notice" markdown='1'>
Remember Rhino is to be used in duel contexts. As an online SaSS application 
running a single instance for multiple clients *and* as a distributable install 
for a single organization. Specific notes for each clients' installation is 
under [client_notes](client_notes)
</div>

## Organizational Objects and Permissions

<div class="notice">
Each user at any time can be part of multiple roles. Their dashboard and views
 should support as many combinations of roles as could be possible.
</div>

### RhinoAccount

The top level `Account`. An account is a grouping of users (such as DNA or BNZ) 
that have access to the software.

In the JustPerformance (SaSS) context, JustPerformance will be an `RhinoAccount`. 

Users can be part of multiple `RhinoAccounts`. If they are, then they'll see a 
 listing view of all their accounts on the `RhinoDashboard`. Once the user has 
selected an account from that dashboard the page is now in the context of that
account.

Administrators attached to a RhinoAccount have full access to the entire 
organization.

### RhinoGroup

Within an account, it's broken down into `RhinoGroup` instances. A group for 
normal operation is a area of expertise such as Development, Design or Sales.
In the JustPerformance context, an `RhinoGroup` relates to a particular client 
they run.

Each group has it's own collective set of `Leaders` that can manage the group 
and act as the account administrators.This includes updating the content and
education material that is required to be completed.

`RhinoGroup` Leaders can also create and assign teams within their group.

### RhinoTeam

Sitting underneath `RhinoGroups` are teams. A team is simply a collection of
users who have to complete the modules and assignments which are given to them.

Team leaders can view the progress of team users and approve, comment on 
assignments. 

## Managing Content

### RhinoCapability

The high level title for an area of learning such as IT or `Design`. To complete
a capability the user has to complete all the modules under a capability. 

### RhinoModule

Contains more relevant information about a certain sub category within a 
Capability. The bulk of the detail and learning tools for a capability is stored
on the module including file downloads and goals. 

To complete a module, the user has to complete all the assignments and mark the
module as completed.

### Assignment

Each module can have one or more assignments, or no assignments attached to it.

Assignments are forms, created by the `RhinoAccount` administrator or the 
`RhinoGroup` leader and are made up for `EditableFormField` objects. Assignments
are saved using the default `UserDefinedForm` API .

## Displaying Content to the User

Content is displayed on the site through a consistent page type `RhinoCrudPage`
(Create, Read, Update, Delete). One of these page types exists for each data 
class. For instance, in the CMS there should be a `RhinoCrudPage` for managing
`RhinoGroup`. These pages are setup upon installation of Rhino by running the
`InstallRhino` task. 

Each type can specify its' own template (i.e so read for Assignment object can
be different to read from Team object). The following is the template hierarchy:

	- `RhinoCrudPage_<DataType>_<action>.ss`
	- `RhinoCrudPage_<action>.ss`
	- `RhinoCrudPage.ss`

The following actions are available:
	
	- view
	- edit
	- remove

On top of those actions the default `index` action is to list all of a given 
type. This is disabled on several types and ultimately depends on whether the 
user should be able to view an index listing of all types.

### RhinoDashboard

RhinoDashboard shows the user a list of all their accounts to select from. Once
the user has selected an account each of the `Crud` records can maintain a 
reference to the current account on the site and populate the `RhinoState` with
the current account reference.