---
name: mixed-arguments
title: Blog Post Generator
description: Generate a blog post on a given topic
arguments:
  - topic
  - name: audience
    description: Target audience for the blog post (e.g. beginners, experts, general public)
    required: false
  - name: length
    description: Desired length in words (e.g. 500, 1000)
    required: false
---
Write a blog post about: {{topic}}.

{{audience}}

{{length}}

The post should have:
- An engaging title
- An introduction paragraph
- 3-5 main sections with subheadings
- A conclusion
