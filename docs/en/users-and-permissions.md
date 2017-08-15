title: User Roles and Permissions
summary: An overview of the user roles and types.

# User Roles and Permissions

All user accounts can belong to multiple groups in this structure at any one 
time so dashboards should be flexible to handle whatever context the users has
been given. For instance, I can be a Team Leader while having to complete 
assignments as a team leader of another team.

## RhinoAccount Administrator

An organization is a representation of an entity who manages the overall account
capabilities, modules and teams within that account. They're effectively an 
administrator with super user rights. 

## Organization Group Leaders

A group of users within a given RhinoAccount. A group will have a set of 
capabilities and modules to complete. Organization Group leaders can create 
teams, modules and set those capabilities. 

Group leaders also have permission to manage teams underneath the group such as
who the team leaders and team members are.

## Team Leaders

Team has members and leaders. Leaders can see progress of their team. They can
belong across multiple teams. Team Leaders create, edit and delete members 
within their team. They are not able to create a new team. They cannot edit or
modify any content on the website.

## Team Members

Team member has to complete assignments.

| |  RhinoAccount Administrators | RhinoGroup Leader  |  Team Leader | Team Member  |
|---|---|---|---|---|---|
| Organization Group - Create  | ✔  |   |   |   | 
| Organization Group - Edit  | ✔  |   |   |   | 
| Organization Group - Delete  | ✔  |   |   |   | 
| Capability - Create | ✔  | ✔ |   |   |
| Capability - Edit | ✔  | ✔ |   |   |
| Capability - Delete | ✔  | ✔ |   |   |
| Module - Create | ✔  | ✔  |   |   |
| Module - Edit | ✔  | ✔  |   |   |
| Module - Delete | ✔  | ✔  |   |   |
| Assignment - Create | ✔ | ✔ |  |  |
| Assignment - Edit | ✔ | ✔ |  |  |
| Assignment - Delete | ✔ | ✔ |  |  |
| Team - Create | ✔ | ✔ |   |  |
| Team - Edit | ✔ | ✔ |  ✔ |  |
| Team - Delete | ✔ | ✔ |   |  |
| Complete Assignment |  |  |   |  ✔ |
| Upload Files | ✔ | ✔ | ✔  |   |