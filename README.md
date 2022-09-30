# Block Template UI
Proof of Concept User Interface for WordPress Block Templates.

## Creating a Page Template
To create a page template just layout you’re blocks the same as you would as normal. Use the meta box to indicate where the template should be applied, and then when you create a post the template will be applied.

Note how blocks can be locked. A PoC plugin exists to restrict this further so a block cannot be changed at all based on user role.

![Create a Template](./assets/create.gif)

## Updating a Page Template
To update a page template, simply amend the template. The next time you come to update a post that uses it you will be advised of updates, and WordPress will apply the changes without impacting any changes you have made.

![Update a Template](./assets/update.gif)

## Template Lock
By default the templates are locked, meaning you cannot change the blocks on the post that uses them. This should be an option when creating the blocks.
