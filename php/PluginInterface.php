<?php
/**
 * Plugin interface.
 *
 * @package   Bandstand
 * @copyright Copyright (c) 2016, AudioTheme, LLC
 * @license   GPL-2.0+
 * @link      https://audiotheme.com/
 * @since     1.0.0
 */

namespace Bandstand;

/**
 * Plugin interface.
 *
 * @package Bandstand
 * @since 1.0.0
 */
interface PluginInterface {
	/**
	 * Load the plugin.
	 *
	 * @return $this
	 */
	public function load();

	/**
	 * Retrieve the absolute path for the main plugin file.
	 *
	 * @return string
	 */
	public function get_basename();

	/**
	 * Set the plugin basename.
	 *
	 * @param string $basename Relative path from the main plugin directory.
	 * @return string
	 */
	public function set_basename( $basename );

	/**
	 * Retrieve the plugin directory.
	 *
	 * @return string
	 */
	public function get_directory();

	/**
	 * Set the plugin's directory.
	 *
	 * @param  string $directory Absolute path to the main plugin directory.
	 * @return $this
	 */
	public function set_directory( $directory );

	/**
	 * Retrieve the path to a file in the plugin.
	 *
	 * @param  string $path Optional. Path relative to the plugin root.
	 * @return string
	 */
	public function get_path( $path = '' );

	/**
	 * Retrieve the absolute path for the main plugin file.
	 *
	 * @return string
	 */
	public function get_file();

	/**
	 * Set the path to the main plugin file.
	 *
	 * @param  string $file Absolute path to the main plugin file.
	 * @return $this
	 */
	public function set_file( $file );

	/**
	 * Retrieve the plugin indentifier.
	 *
	 * @return string
	 */
	public function get_slug();

	/**
	 * Set the plugin identifier.
	 *
	 * @param  string $slug Plugin identifier.
	 * @return $this
	 */
	public function set_slug( $slug );

	/**
	 * Retrieve the URL for a file in the plugin.
	 *
	 * @param  string $path Optional. Path relative to the plugin root.
	 * @return string
	 */
	public function get_url( $path = '' );

	/**
	 * Set the URL for plugin directory root.
	 *
	 * @param  string $url URL to the root of the plugin directory.
	 * @return $this
	 */
	public function set_url( $url );

	/**
	 * Register hooks for the plugin.
	 *
	 * @param HookProviderInterface $provider Hook provider instance.
	 */
	public function register_hooks( HookProviderInterface $provider );
}
