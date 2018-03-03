# Beans Visual Hook Guide 
## For the Beans WordPress Theme Framework
**Visual Hook Guide for the Beans HTML API**

The Beans Visual Hook Guide is an experimental plugin based on the idea of the Genesis Visual Hook Guide.

[Beans](http://www.getbeans.io/) is an incredibly powerful and flexible WordPress theme, yet light weight and unbelievably fast. 
Whether you are a pro or a beginner, you will enjoy the simplicity of it. The magic is under the hood!

Beans includes an amazing 'Development Mode', which when enabled provides a 'data-markup-id' attribute for each HTML element.
This can be viewed in the Browser inspector and used for hooking Beans Actions and Filters to.

Strictly speaking, this functionality makes this plugin fairly redundant, we should be comfortable in the inspector.
However, I wanted to play around, see if I could create a plugin that complimented this feature. 

Work in progress...

**Display all Beans HTML API Action Hooks**
- Display all at once
- Display individually - only display the hooks you want
    - All possible hooks are listed in WP Toolbar drop-down
- Color coded
    - Red for prepend/append hooks
    - Blue for before/after hooks
    - Orange border around the markup element with the data-markup-id attribute 


**Requires development mode to be enabled**

**Disables itself if theme is changed to non Beans framework child theme**






