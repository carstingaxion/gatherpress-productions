# GatherPress Productions

**Contributors:** carstenbach  
**Tags:** theater, productions, gatherpress  
**Tested up to:** 6.9  
**Stable tag:** 0.3.2  
**License:** GPLv2 or later  
**License URI:** https://www.gnu.org/licenses/gpl-2.0.html  

[![Playground Demo Link](https://img.shields.io/badge/WordPress_Playground-blue?logo=wordpress&logoColor=%23fff&labelColor=%233858e9&color=%233858e9)](https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/carstingaxion/gatherpress-productions/main/.wordpress-org/blueprints/blueprint.json) [![Build, test & measure](https://github.com/carstingaxion/gatherpress-productions/actions/workflows/build-test-measure.yml/badge.svg?branch=main)](https://github.com/carstingaxion/gatherpress-productions/actions/workflows/build-test-measure.yml)


## Description

GatherPress Productions extends [GatherPress](https://gatherpress.org/) for **theater and performing-arts use cases**. It adds a dedicated **Production** content type and wires it tightly into GatherPress's event system so that every production can carry a premiere date, a lifecycle status, and a two-way relationship with regular GatherPress events.

### What it does

**Production post type (`gatherpress_play`)**  
Registers a public, block-editor-enabled post type called *Production* (hierarchical, with archive, configurable permalink base). Productions support a featured image — labelled *Production Poster* in the UI — as well as title, content, excerpt, custom fields, and revisions.

**Premiere date instead of "Event date"**  
On Production posts, GatherPress's built-in date picker and admin list column are re-labelled *Premiere* (via both PHP and a JavaScript filter on the editor sidebar panel title). The authoring UI feels native to a theater context while reusing all of GatherPress's date and ordering logic.

**Shadow taxonomy linking productions to events**  
A private shadow taxonomy (`_gatherpress_play`) is registered on the `gatherpress_event` post type. GatherPress's shadow-taxonomy mechanism uses it to connect individual events back to the production they belong to, making it possible to query all events for a given production.

**Production Status taxonomy**  
A `production_status` taxonomy tracks a production through its lifecycle. Four default terms are created automatically on activation: *Pre-Production*, *In Rehearsal*, *Running*, and *Closed*. When a production's premiere date passes (triggered by the `gatherpress_event_ended` action), the status is automatically advanced to *Running*.

**Block variation: Production Details**  
Registers a JavaScript block variation of `gatherpress/venue` named *Production* (or whatever the singular post-type label is). The variation sets `sourcePostType` to `gatherpress_play` and ships default inner blocks (linked post title + linked featured image at heading level 3), giving editors a ready-made Production context block in the inserter.

**Re-labelled editor UI**  
A small JavaScript filter (`gatherpress.eventSettingsPanelTitle`) renames the *Event Settings* sidebar panel to *Premiere* when editing a Production, keeping the language consistent throughout the editor.

**Settings sub-page**  
Adds a *Theater* section under the GatherPress settings screen with a *Permalinks* option so site administrators can customise the URL base for the Productions archive (defaults to the translated word for "Production").

**Starter block pattern**  
Registers a `gatherpress-productions/starter` block pattern scoped to the Production post type, giving authors an optional starting layout when creating a new production.


## Requirements

- WordPress 6.4 or later
- PHP 7.4 or later
- [GatherPress](https://gatherpress.org/) 0.34.0-alpha-2 or later

## Installation

1. Upload the plugin files to `/wp-content/plugins/gatherpress-productions`.
2. Activate the plugin via the **Plugins** screen.


## Frequently Asked Questions

### Does this work without GatherPress?

No.

## Changelog

All notable changes to this project will be documented in the [CHANGELOG.md](CHANGELOG.md).

## License

This plugin is licensed under the [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html).
