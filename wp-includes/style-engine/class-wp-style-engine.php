                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                clarations.
	 * }
	 * @return array {
	 *     @type string[] $classnames   Array of class names.
	 *     @type string[] $declarations An associative array of CSS definitions,
	 *                                  e.g. `array( "$property" => "$value", "$property" => "$value" )`.
	 * }
	 */
	public static function parse_block_styles( $block_styles, $options ) {
		$parsed_styles = array(
			'classnames'   => array(),
			'declarations' => array(),
		);
		if ( empty( $block_styles ) || ! is_array( $block_styles ) ) {
			return $parsed_styles;
		}

		// Collect CSS and classnames.
		foreach ( static::BLOCK_STYLE_DEFINITIONS_METADATA as $definition_group_key => $definition_group_style ) {
			if ( empty( $block_styles[ $definition_group_key ] ) ) {
				continue;
			}
			foreach ( $definition_group_style as $style_definition ) {
				$style_value = _wp_array_get( $block_styles, $style_definition['path'], null );

				if ( ! static::is_valid_style_value( $style_value ) ) {
					continue;
				}

				$parsed_styles['classnames']   = array_merge( $parsed_styles['classnames'], static::get_classnames( $style_value, $style_definition ) );
				$parsed_styles['declarations'] = array_merge( $parsed_styles['declarations'], static::get_css_declarations( $style_value, $style_definition, $options ) );
			}
		}

		return $parsed_styles;
	}

	/**
	 * Returns classnames, and generates classname(s) from a CSS preset property pattern,
	 * e.g. `var:preset|<PRESET_TYPE>|<PRESET_SLUG>`.
	 *
	 * @since 6.1.0
	 *
	 * @param string $style_value      A single raw style value or CSS preset property
	 *                                 from the `$block_styles` array.
	 * @param array  $style_definition A single style definition from BLOCK_STYLE_DEFINITIONS_METADATA.
	 * @return string[] An array of CSS classnames, or empty array if there are none.
	 */
	protected static function get_classnames( $style_value, $style_definition ) {
		if ( empty( $style_value ) ) {
			return array();
		}

		$classnames = array();
		if ( ! empty( $style_definition['classnames'] ) ) {
			foreach ( $style_definition['classnames'] as $classname => $property_key ) {
				if ( true === $property_key ) {
					$classnames[] = $classname;
				}

				$slug = static::get_slug_from_preset_value( $style_value, $property_key );

				if ( $slug ) {
					/*
					 * Right now we expect a classname pattern to be stored in BLOCK_STYLE_DEFINITIONS_METADATA.
					 * One day, if there are no stored schemata, we could allow custom patterns or
					 * generate classnames based on other properties
					 * such as a path or a value or a prefix passed in options.
					 */
					$classnames[] = strtr( $classname, array( '$slug' => $slug ) );
				}
			}
		}

		return $classnames;
	}

	/**
	 * Returns an array of CSS declarations based on valid block style values.
	 *
	 * @since 6.1.0
	 *
	 * @param mixed $style_value      A single raw style value from $block_styles array.
	 * @param array $style_definition A single style definition from BLOCK_STYLE_DEFINITIONS_METADATA.
	 * @param array $options          {
	 *     Optional. An array of options. Default empty array.
	 *
	 *     @type bool $convert_vars_to_classnames Whether to skip converting incoming CSS var patterns,
	 *                                            e.g. `var:preset|<PRESET_TYPE>|<PRESET_SLUG>`,
	 *                                            to `var( --wp--preset--* )` values. Default false.
	 * }
	 * @return string[] An associative array of CSS definitions, e.g. `array( "$property" => "$value", "$property" => "$value" )`.
	 */
	protected static function get_css_declarations( $style_value, $style_definition, $options = array() ) {
		if ( isset( $style_definition['value_func'] ) && is_callable( $style_definition['value_func'] ) ) {
			return call_user_func( $style_definition['value_func'], $style_value, $style_definition, $options );
		}

		$css_declarations     = array();
		$style_property_keys  = $style_definition['property_keys'];
		$should_skip_css_vars = isset( $options['convert_vars_to_classnames'] ) && true === $options['convert_vars_to_classnames'];

		/*
		 * Build CSS var values from `var:preset|<PRESET_TYPE>|<PRESET_SLUG>` values, e.g, `var(--wp--css--rule-slug )`.
		 * Check if the value is a CSS preset and there's a corresponding css_var pattern in the style definition.
		 */
		if ( is_string( $style_value ) && str_contains( $style_value, 'var:' ) ) {
			if ( ! $should_skip_css_vars && ! empty( $style_definition['css_vars'] ) ) {
				$css_var = static::get_css_var_value( $style_value, $style_definition['css_vars'] );
				if ( static::is_valid_style_value( $css_var ) ) {
					$css_declarations[ $style_property_keys['default'] ] = $css_var;
				}
			}
			return $css_declarations;
		}

		/*
		 * Default rule builder.
		 * If the input contains an array, assume box model-like properties
		 * for styles such as margins and padding.
		 */
		if ( is_array( $style_value ) ) {
			// Bail out early if the `'individual'` property is not defined.
			if ( ! isset( $style_property_keys['individual'] ) ) {
				return $css_declarations;
			}

			foreach ( $style_value as $key => $value ) {
				if ( is_string( $value ) && str_contains( $value, 'var:' ) && ! $should_skip_css_vars && ! empty( $style_definition['css_vars'] ) ) {
					$value = static::get_css_var_value( $value, $style_definition['css_vars'] );
				}

				$individual_property = sprintf( $style_property_keys['individual'], _wp_to_kebab_case( $key ) );

				if ( $individual_property && static::is_valid_style_value( $value ) ) {
					$css_declarations[ $individual_property ] = $value;
				}
			}

			return $css_declarations;
		}

		$css_declarations[ $style_property_keys['default'] ] = $style_value;
		return $css_declarations;
	}

	/**
	 * Style value parser that returns a CSS definition array comprising style properties
	 * that have keys representing individual style properties, otherwise known as longhand CSS properties.
	 *
	 * Example:
	 *
	 *     "$style_property-$individual_feature: $value;"
	 *
	 * Which could represent the following:
	 *
	 *     "border-{top|right|bottom|left}-{color|width|style}: {value};"
	 *
	 * or:
	 *
	 *     "border-image-{outset|source|width|repeat|slice}: {value};"
	 *
	 * @since 6.1.0
	 *
	 * @param array $style_value                    A single raw style value from `$block_styles` array.
	 * @param array $individual_property_definition A single style definition from BLOCK_STYLE_DEFINITIONS_METADATA
	 *                                              representing an individual property of a CSS property,
	 *                                              e.g. 'top' in 'border-top'.
	 * @param array $options                        {
	 *     Optional. An array of options. Default empty array.
	 *
	 *     @type bool $convert_vars_to_classnames Whether to skip converting incoming CSS var patterns,
	 *                                            e.g. `var:preset|<PRESET_TYPE>|<PRESET_SLUG>`,
	 *                                            to `var( --wp--preset--* )` values. Default false.
	 * }
	 * @return string[] An associative array of CSS definitions, e.g. `array( "$property" => "$value", "$property" => "$value" )`.
	 */
	protected static function get_individual_property_css_declarations( $style_value, $individual_property_definition, $options = array() ) {
		if ( ! is_array( $style_value ) || empty( $style_value ) || empty( $individual_property_definition['path'] ) ) {
			return array();
		}

		/*
		 * The first item in $individual_property_definition['path'] array
		 * tells us the style property, e.g. "border". We use this to get a corresponding
		 * CSS style definition such as "color" or "width" from the same group.
		 *
		 * The second item in $individual_property_definition['path'] array
		 * refers to the individual property marker, e.g. "top".
		 */
		$definition_group_key    = $individual_property_definition['path'][0];
		$individual_property_key = $individual_property_definition['path'][1];
		$should_skip_css_vars    = isset( $options['convert_vars_to_classnames'] ) && true === $options['convert_vars_to_classnames'];
		$css_declarations        = array();

		foreach ( $style_value as $css_property => $value ) {
			if ( empty( $value ) ) {
				continue;
			}

			// Build a path to the individual rules in definitions.
			$style_definition_path = array( $definition_group_key, $css_property );
			$style_definition      = _wp_array_get( static::BLOCK_STYLE_DEFINITIONS_METADATA, $style_definition_path, null );

			if ( $style_definition && isset( $style_definition['property_keys']['individual'] ) ) {
				// Set a CSS var if there is a valid preset value.
				if ( is_string( $value ) && str_contains( $value, 'var:' )
					&& ! $should_skip_css_vars && ! empty( $individual_property_definition['css_vars'] )
				) {
					$value = static::get_css_var_value( $value, $individual_property_definition['css_vars'] );
				}

				$individual_css_property = sprintf( $style_definition['property_keys']['individual'], $individual_property_key );

				$css_declarations[ $individual_css_property ] = $value;
			}
		}
		return $css_declarations;
	}

	/**
	 * Returns compiled CSS from CSS declarations.
	 *
	 * @since 6.1.0
	 *
	 * @param string[] $css_declarations An associative array of CSS definitions,
	 *                                   e.g. `array( "$property" => "$value", "$property" => "$value" )`.
	 * @param string   $css_selector     When a selector is passed, the function will return
	 *                                   a full CSS rule `$selector { ...rules }`,
	 *                                   otherwise a concatenated string of properties and values.
	 * @return string A compiled CSS string.
	 */
	public static function compile_css( $css_declarations, $css_selector ) {
		if ( empty( $css_declarations ) || ! is_array( $css_declarations ) ) {
			return '';
		}

		// Return an entire rule if there is a selector.
		if ( $css_selector ) {
			$css_rule = new WP_Style_Engine_CSS_Rule( $css_selector, $css_declarations );
			return $css_rule->get_css();
		}

		$css_declarations = new WP_Style_Engine_CSS_Declarations( $css_declarations );
		return $css_declarations->get_declarations_string();
	}

	/**
	 * Returns a compiled stylesheet from stored CSS rules.
	 *
	 * @since 6.1.0
	 *
	 * @param WP_Style_Engine_CSS_Rule[] $css_rules An array of WP_Style_Engine_CSS_Rule objects
	 *                                              from a store or otherwise.
	 * @param array                      $options   {
	 *     Optional. An array of options. Default empty array.
	 *
	 *     @type string|null $context  An identifier describing the origin of the style object,
	 *                                 e.g. 'block-supports' or 'global-styles'. Default 'block-supports'.
	 *                                 When set, the style engine will attempt to store the CSS rules.
	 *     @type bool        $optimize Whether to optimize the CSS output, e.g. combine rules.
	 *                                 Default true.
	 *     @type bool        $prettify Whether to add new lines and indents to output.
	 *                                 Defaults to whether the `SCRIPT_DEBUG` constant is defined.
	 * }
	 * @return string A compiled stylesheet from stored CSS rules.
	 */
	public static function compile_stylesheet_from_css_rules( $css_rules, $options = array() ) {
		$processor = new WP_Style_Engine_Processor();
		$processor->add_rules( $css_rules );
		return $processor->get_css( $options );
	}
}
