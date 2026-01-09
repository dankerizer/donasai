# Plugin Check Report

**Plugin:** Donasai - Platform Donasi & Penggalangan Dana
**Generated at:** 2026-01-08 21:32:18


## `includes/gateways/manual.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 63 | 21 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 126 | 21 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 126 | 28 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table used in $wpdb-&gt;get_row($wpdb-&gt;prepare(&quot;SELECT * FROM {$table} WHERE id = %d&quot;, $donation_id))\n$table assigned unsafely at line 124:\n $table = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$donation assigned unsafely at line 126:\n $donation = $wpdb-&gt;get_row($wpdb-&gt;prepare(&quot;SELECT * FROM {$table} WHERE id = %d&quot;, $donation_id))\n$donation_id used without escaping. |  |
| 163 | 33 | ERROR | WordPress.WP.I18n.MissingTranslatorsComment | A function call to esc_html__() with texts containing placeholders was found, but was not accompanied by a "translators:" comment on the line above to clarify the meaning of the placeholders. | [Docs](https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/#descriptions) |

## `frontend/templates/confirmation-form.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 29 | 33 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'home_url'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |

## `frontend/templates/receipt.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 9 | 22 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 9 | 53 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 15 | 17 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 15 | 17 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 15 | 24 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table used in $wpdb-&gt;get_row($wpdb-&gt;prepare(&quot;SELECT * FROM $table WHERE id = %d&quot;, $donation_id))\n$table assigned unsafely at line 14:\n $table = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$donation assigned unsafely at line 15:\n $donation = $wpdb-&gt;get_row($wpdb-&gt;prepare(&quot;SELECT * FROM $table WHERE id = %d&quot;, $donation_id)) |  |
| 15 | 47 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table at &quot;SELECT * FROM $table WHERE id = %d&quot; |  |
| 168 | 29 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$donation'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |

## `frontend/templates/donation-form.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 172 | 33 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 172 | 63 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 1046 | 38 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$snap_url'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |

## `frontend/templates/campaign-single.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 274 | 79 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$campaign_id'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 280 | 53 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'wp_login_url'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 407 | 37 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'wp_create_nonce'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |

## `frontend/templates/donation-summary.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 22 | 13 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 22 | 20 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table used in $wpdb-&gt;get_row($wpdb-&gt;prepare(&quot;SELECT * FROM {$table} WHERE id = %d&quot;, $donation_id))\n$table assigned unsafely at line 20:\n $table = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$table assigned unsafely at line 16:\n $table = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$donation assigned unsafely at line 22:\n $donation = $wpdb-&gt;get_row($wpdb-&gt;prepare(&quot;SELECT * FROM {$table} WHERE id = %d&quot;, $donation_id))\n$donation_id assigned unsafely at line 12:\n $donation_id = get_query_var($thankyou_slug)\n$thankyou_slug assigned unsafely at line 11:\n $thankyou_slug = get_option(&#039;wpd_settings_general&#039;)[&#039;thankyou_slug&#039;] ?? &#039;thank-you&#039; |  |
| 22 | 43 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table} at &quot;SELECT * FROM {$table} WHERE id = %d&quot; |  |
| 263 | 29 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$js_conf_url'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 264 | 29 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$js_phone'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 265 | 29 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$js_donation_id'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 266 | 29 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$js_amount'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |

## `includes/metabox.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 105 | 55 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found '$packages'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 224 | 23 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;wpd_campaign_options_nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 224 | 23 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;wpd_campaign_options_nonce&#039;] |  |
| 257 | 12 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;wpd_pixel_ids&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 257 | 12 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;wpd_pixel_ids&#039;] |  |
| 267 | 12 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;wpd_whatsapp_settings&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 267 | 12 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;wpd_whatsapp_settings&#039;] |  |
| 283 | 7 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;wpd_campaign_banks&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 283 | 7 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;wpd_campaign_banks&#039;] |  |

## `includes/admin/campaign-columns.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 71 | 89 | ERROR | WordPress.Security.EscapeOutput.OutputNotEscaped | All output should be run through an escaping function (see the Security sections in the WordPress Developer Handbooks), found 'min'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-functions) |
| 190 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |

## `includes/functions-frontend.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 18 | 15 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 31 | 19 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 50 | 19 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 137 | 149 | WARNING | WordPress.WP.EnqueuedResourceParameters.MissingVersion | Resource version not set in call to wp_enqueue_style(). This means new versions of the style may not always be loaded due to browser caching. |  |
| 143 | 19 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 176 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 176 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 176 | 23 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table used in $wpdb-&gt;get_results($wpdb-&gt;prepare(\n &quot;SELECT * FROM $table WHERE campaign_id = %d AND status = &#039;complete&#039; ORDER BY created_at DESC LIMIT %d&quot;,\n $campaign_id,\n $limit\n ))\n$table assigned unsafely at line 172:\n $table = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$results assigned unsafely at line 176:\n $results = $wpdb-&gt;get_results($wpdb-&gt;prepare(\n &quot;SELECT * FROM $table WHERE campaign_id = %d AND status = &#039;complete&#039; ORDER BY created_at DESC LIMIT %d&quot;,\n $campaign_id,\n $limit\n ))\n$campaign_id used without escaping.\n$limit used without escaping. |  |
| 177 | 9 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table at &quot;SELECT * FROM $table WHERE campaign_id = %d AND status = &#039;complete&#039; ORDER BY created_at DESC LIMIT %d&quot; |  |
| 287 | 15 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 288 | 52 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 321 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 321 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 321 | 23 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_fundraisers used in $wpdb-&gt;get_results($wpdb-&gt;prepare(\n &quot;SELECT f.*, p.post_title \n FROM $table_fundraisers f\n JOIN {$wpdb-&gt;posts} p ON f.campaign_id = p.ID\n WHERE f.user_id = %d\n ORDER BY f.created_at DESC&quot;,\n $user_id\n ))\n$table_fundraisers assigned unsafely at line 317:\n $table_fundraisers = $wpdb-&gt;prefix . &#039;wpd_fundraisers&#039;\n$results assigned unsafely at line 321:\n $results = $wpdb-&gt;get_results($wpdb-&gt;prepare(\n &quot;SELECT f.*, p.post_title \n FROM $table_fundraisers f\n JOIN {$wpdb-&gt;posts} p ON f.campaign_id = p.ID\n WHERE f.user_id = %d\n ORDER BY f.created_at DESC&quot;,\n $user_id\n ))\n$user_id assigned unsafely at line 316:\n $user_id = get_current_user_id() |  |
| 323 | 1 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table_fundraisers at FROM $table_fundraisers f\n |  |
| 337 | 19 | ERROR | WordPress.Security.EscapeOutput.UnsafePrintingFunction | All output should be run through an escaping function (like esc_html_e() or esc_attr_e()), found '_e'. | [Docs](https://developer.wordpress.org/apis/security/escaping/#escaping-with-localization) |
| 352 | 36 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 352 | 36 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 393 | 30 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;wpd_profile_nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 393 | 30 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;wpd_profile_nonce&#039;] |  |
| 398 | 48 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_POST[&#039;display_name&#039;]. Check that the array index exists before using it. |  |
| 399 | 49 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_POST[&#039;phone&#039;]. Check that the array index exists before using it. |  |
| 400 | 29 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_POST[&#039;pass1&#039;]. Check that the array index exists before using it. |  |
| 400 | 29 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;pass1&#039;] |  |
| 401 | 29 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_POST[&#039;pass2&#039;]. Check that the array index exists before using it. |  |
| 401 | 29 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;pass2&#039;] |  |
| 430 | 132 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;wpd_profile_error&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 430 | 132 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;wpd_profile_error&#039;] |  |
| 454 | 25 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 454 | 25 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 462 | 30 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;_wpnonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 462 | 30 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;_wpnonce&#039;] |  |
| 466 | 35 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_POST[&#039;donation_id&#039;]. Check that the array index exists before using it. |  |
| 467 | 62 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_POST[&#039;amount&#039;]. Check that the array index exists before using it. |  |
| 467 | 62 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;amount&#039;] |  |
| 471 | 25 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 471 | 25 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 481 | 33 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_FILES[&#039;proof_file&#039;]. Check that the array index exists before using it. |  |
| 481 | 33 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_FILES[&#039;proof_file&#039;] |  |
| 490 | 67 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_POST[&#039;sender_bank&#039;]. Check that the array index exists before using it. |  |
| 491 | 67 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_POST[&#039;sender_name&#039;]. Check that the array index exists before using it. |  |
| 506 | 21 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 506 | 21 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |

## `includes/api/donations-controller.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 63 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 63 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 63 | 23 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;get_results(&quot;\n SELECT \n DATE(created_at) as date, \n SUM(amount) as total_amount,\n COUNT(id) as total_count\n FROM $table_donations \n WHERE status = &#039;complete&#039; \n AND created_at &gt;= DATE_SUB(NOW(), INTERVAL 30 DAY)\n GROUP BY DATE(created_at)\n ORDER BY date ASC\n &quot;)\n$table_donations assigned unsafely at line 60:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$results assigned unsafely at line 63:\n $results = $wpdb-&gt;get_results(&quot;\n SELECT \n DATE(created_at) as date, \n SUM(amount) as total_amount,\n COUNT(id) as total_count\n FROM $table_donations \n WHERE status = &#039;complete&#039; \n AND created_at &gt;= DATE_SUB(NOW(), INTERVAL 30 DAY)\n GROUP BY DATE(created_at)\n ORDER BY date ASC\n &quot;) |  |
| 68 | 1 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table_donations at FROM $table_donations \n |  |
| 106 | 24 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 106 | 24 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 106 | 31 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;get_results(&quot;\n SELECT payment_method, COUNT(*) as count \n FROM $table_donations \n WHERE status = &#039;complete&#039; \n GROUP BY payment_method\n &quot;)\n$table_donations assigned unsafely at line 60:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$results assigned unsafely at line 63:\n $results = $wpdb-&gt;get_results(&quot;\n SELECT \n DATE(created_at) as date, \n SUM(amount) as total_amount,\n COUNT(id) as total_count\n FROM $table_donations \n WHERE status = &#039;complete&#039; \n AND created_at &gt;= DATE_SUB(NOW(), INTERVAL 30 DAY)\n GROUP BY DATE(created_at)\n ORDER BY date ASC\n &quot;) |  |
| 108 | 1 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table_donations at FROM $table_donations \n |  |
| 114 | 22 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 114 | 22 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 114 | 29 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;get_results(&quot;\n SELECT p.post_title as name, SUM(d.amount) as value\n FROM $table_donations d\n LEFT JOIN {$wpdb-&gt;posts} p ON d.campaign_id = p.ID\n WHERE d.status = &#039;complete&#039; AND d.campaign_id &gt; 0\n GROUP BY d.campaign_id\n ORDER BY value DESC\n LIMIT 5\n &quot;)\n$table_donations assigned unsafely at line 60:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$results assigned unsafely at line 63:\n $results = $wpdb-&gt;get_results(&quot;\n SELECT \n DATE(created_at) as date, \n SUM(amount) as total_amount,\n COUNT(id) as total_count\n FROM $table_donations \n WHERE status = &#039;complete&#039; \n AND created_at &gt;= DATE_SUB(NOW(), INTERVAL 30 DAY)\n GROUP BY DATE(created_at)\n ORDER BY date ASC\n &quot;) |  |
| 116 | 1 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table_donations at FROM $table_donations d\n |  |
| 138 | 24 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 138 | 24 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 138 | 31 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;get_var(&quot;SELECT SUM(amount) FROM $table_donations WHERE status = &#039;complete&#039;&quot;)\n$table_donations assigned unsafely at line 134:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$table_subscriptions assigned unsafely at line 135:\n $table_subscriptions = $wpdb-&gt;prefix . &#039;wpd_subscriptions&#039;\n$total_collected assigned unsafely at line 138:\n $total_collected = $wpdb-&gt;get_var(&quot;SELECT SUM(amount) FROM $table_donations WHERE status = &#039;complete&#039;&quot;) |  |
| 138 | 39 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table_donations at &quot;SELECT SUM(amount) FROM $table_donations WHERE status = &#039;complete&#039;&quot; |  |
| 141 | 21 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 141 | 21 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 141 | 28 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;get_var(&quot;SELECT COUNT(DISTINCT email) FROM $table_donations WHERE status = &#039;complete&#039;&quot;)\n$table_donations assigned unsafely at line 134:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$table_subscriptions assigned unsafely at line 135:\n $table_subscriptions = $wpdb-&gt;prefix . &#039;wpd_subscriptions&#039;\n$total_collected assigned unsafely at line 138:\n $total_collected = $wpdb-&gt;get_var(&quot;SELECT SUM(amount) FROM $table_donations WHERE status = &#039;complete&#039;&quot;) |  |
| 141 | 36 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table_donations at &quot;SELECT COUNT(DISTINCT email) FROM $table_donations WHERE status = &#039;complete&#039;&quot; |  |
| 153 | 29 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 153 | 29 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 153 | 36 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;get_var($wpdb-&gt;prepare(\n &quot;SELECT SUM(amount) FROM $table_donations WHERE status = &#039;complete&#039; AND created_at &gt;= %s&quot;,\n $current_month_start\n ))\n$table_donations assigned unsafely at line 134:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$table_subscriptions assigned unsafely at line 135:\n $table_subscriptions = $wpdb-&gt;prefix . &#039;wpd_subscriptions&#039;\n$total_collected assigned unsafely at line 138:\n $total_collected = $wpdb-&gt;get_var(&quot;SELECT SUM(amount) FROM $table_donations WHERE status = &#039;complete&#039;&quot;) |  |
| 154 | 9 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table_donations at &quot;SELECT SUM(amount) FROM $table_donations WHERE status = &#039;complete&#039; AND created_at &gt;= %s&quot; |  |
| 158 | 26 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 158 | 26 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 158 | 33 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;get_var($wpdb-&gt;prepare(\n &quot;SELECT SUM(amount) FROM $table_donations WHERE status = &#039;complete&#039; AND created_at &gt;= %s AND created_at &lt;= %s&quot;,\n $last_month_start,\n $last_month_end\n ))\n$table_donations assigned unsafely at line 134:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$table_subscriptions assigned unsafely at line 135:\n $table_subscriptions = $wpdb-&gt;prefix . &#039;wpd_subscriptions&#039;\n$total_collected assigned unsafely at line 138:\n $total_collected = $wpdb-&gt;get_var(&quot;SELECT SUM(amount) FROM $table_donations WHERE status = &#039;complete&#039;&quot;) |  |
| 159 | 9 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table_donations at &quot;SELECT SUM(amount) FROM $table_donations WHERE status = &#039;complete&#039; AND created_at &gt;= %s AND created_at &lt;= %s&quot; |  |
| 174 | 9 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 174 | 9 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 174 | 16 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_subscriptions used in $wpdb-&gt;get_var(&quot;SHOW TABLES LIKE &#039;$table_subscriptions&#039;&quot;)\n$table_subscriptions assigned unsafely at line 135:\n $table_subscriptions = $wpdb-&gt;prefix . &#039;wpd_subscriptions&#039;\n$total_collected assigned unsafely at line 138:\n $total_collected = $wpdb-&gt;get_var(&quot;SELECT SUM(amount) FROM $table_donations WHERE status = &#039;complete&#039;&quot;)\n$table_donations assigned unsafely at line 134:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039; |  |
| 174 | 24 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table_subscriptions at &quot;SHOW TABLES LIKE &#039;$table_subscriptions&#039;&quot; |  |
| 175 | 37 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_subscriptions used in $wpdb-&gt;get_var(&quot;SELECT SUM(amount) FROM $table_subscriptions WHERE status = &#039;active&#039;&quot;)\n$table_subscriptions assigned unsafely at line 135:\n $table_subscriptions = $wpdb-&gt;prefix . &#039;wpd_subscriptions&#039;\n$total_collected assigned unsafely at line 138:\n $total_collected = $wpdb-&gt;get_var(&quot;SELECT SUM(amount) FROM $table_donations WHERE status = &#039;complete&#039;&quot;)\n$table_donations assigned unsafely at line 134:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039; |  |
| 175 | 45 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table_subscriptions at &quot;SELECT SUM(amount) FROM $table_subscriptions WHERE status = &#039;active&#039;&quot; |  |
| 180 | 22 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 180 | 22 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 180 | 29 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;get_var(&quot;\n SELECT COUNT(*) FROM (\n SELECT email FROM $table_donations \n WHERE status = &#039;complete&#039; \n GROUP BY email \n HAVING COUNT(id) &gt; 1\n ) as repeaters\n &quot;)\n$table_donations assigned unsafely at line 134:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$table_subscriptions assigned unsafely at line 135:\n $table_subscriptions = $wpdb-&gt;prefix . &#039;wpd_subscriptions&#039;\n$total_collected assigned unsafely at line 138:\n $total_collected = $wpdb-&gt;get_var(&quot;SELECT SUM(amount) FROM $table_donations WHERE status = &#039;complete&#039;&quot;) |  |
| 182 | 1 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table_donations at SELECT email FROM $table_donations \n |  |
| 252 | 41 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 252 | 41 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;_wpnonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 252 | 41 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_GET[&#039;_wpnonce&#039;] |  |
| 259 | 56 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;campaign_id&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 259 | 56 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_GET[&#039;campaign_id&#039;] |  |
| 260 | 46 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;status&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 260 | 46 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_GET[&#039;status&#039;] |  |
| 261 | 54 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;start_date&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 261 | 54 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_GET[&#039;start_date&#039;] |  |
| 262 | 50 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;end_date&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 262 | 50 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_GET[&#039;end_date&#039;] |  |
| 268 | 33 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table at &quot;SELECT * FROM $table WHERE {$query_parts[&#039;where&#039;]} ORDER BY created_at DESC&quot; |  |
| 268 | 33 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$query_parts[&#039;where&#039;]} at &quot;SELECT * FROM $table WHERE {$query_parts[&#039;where&#039;]} ORDER BY created_at DESC&quot; |  |
| 268 | 110 | WARNING | WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare | Replacement variables found, but no valid placeholders found in the query. |  |
| 273 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 273 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 273 | 23 | ERROR | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $query used in $wpdb->get_results($query)\n$query assigned unsafely at line 270:\n $query = "SELECT * FROM $table ORDER BY created_at DESC"\n$table assigned unsafely at line 249:\n $table = $wpdb->prefix . 'wpd_donations'\n$_GET['_wpnonce'] used without escaping. |  |
| 273 | 35 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $query |  |
| 299 | 5 | ERROR | WordPress.WP.AlternativeFunctions.file_system_operations_fclose | File operations should use WP_Filesystem methods instead of direct PHP filesystem calls. Found: fclose(). |  |
| 333 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 333 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 350 | 24 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 350 | 24 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 350 | 31 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table used in $wpdb-&gt;get_var($wpdb-&gt;prepare(&quot;SELECT campaign_id FROM $table WHERE id = %d&quot;, $id))\n$table assigned unsafely at line 306:\n $table = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$id assigned unsafely at line 307:\n $id = $request[&#039;id&#039;]\n$request[&#039;id&#039;] used without escaping. |  |
| 350 | 54 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table at &quot;SELECT campaign_id FROM $table WHERE id = %d&quot; |  |
| 359 | 20 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 359 | 20 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 359 | 27 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table used in $wpdb-&gt;get_row($wpdb-&gt;prepare(&quot;SELECT * FROM $table WHERE id = %d&quot;, $id))\n$table assigned unsafely at line 306:\n $table = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$id assigned unsafely at line 307:\n $id = $request[&#039;id&#039;]\n$request[&#039;id&#039;] used without escaping. |  |
| 359 | 50 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table at &quot;SELECT * FROM $table WHERE id = %d&quot; |  |
| 396 | 33 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table at &quot;SELECT * FROM $table WHERE {$query_parts[&#039;where&#039;]} ORDER BY created_at DESC&quot; |  |
| 396 | 33 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$query_parts[&#039;where&#039;]} at &quot;SELECT * FROM $table WHERE {$query_parts[&#039;where&#039;]} ORDER BY created_at DESC&quot; |  |
| 396 | 110 | WARNING | WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare | Replacement variables found, but no valid placeholders found in the query. |  |
| 401 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 401 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 401 | 23 | ERROR | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $query used in $wpdb->get_results($query)\n$query assigned unsafely at line 398:\n $query = "SELECT * FROM $table ORDER BY created_at DESC"\n$table assigned unsafely at line 383:\n $table = $wpdb->prefix . 'wpd_donations'\n$params assigned unsafely at line 386:\n $params = array(\n 'campaign_id' => $request->get_param('campaign_id'),\n 'status' => $request->get_param('status'),\n 'start_date' => $request->get_param('start_date'),\n 'end_date' => $request->get_param('end_date'),\n )\n$request used without escaping. |  |
| 401 | 35 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $query |  |

## `includes/services/subscription.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 35 | 21 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 60 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 60 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 60 | 23 | ERROR | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $sql used in $wpdb->get_results($wpdb->prepare($sql, $user_id))\n$sql assigned unsafely at line 54:\n $sql = "SELECT s.*, p.post_title as campaign_title \n FROM $table s\n JOIN $table_posts p ON s.campaign_id = p.ID\n WHERE s.user_id = %d \n ORDER BY s.created_at DESC"\n$table assigned unsafely at line 50:\n $table = $wpdb->prefix . 'wpd_subscriptions'\n$table_posts assigned unsafely at line 51:\n $table_posts = $wpdb->prefix . 'posts' |  |
| 60 | 50 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $sql |  |
| 71 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 71 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 89 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 89 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 89 | 23 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table used in $wpdb-&gt;get_results(&quot;SELECT * FROM $table WHERE status = &#039;active&#039; AND next_payment_date &lt;= NOW()&quot;)\n$table assigned unsafely at line 85:\n $table = $wpdb-&gt;prefix . &#039;wpd_subscriptions&#039;\n$due assigned unsafely at line 89:\n $due = $wpdb-&gt;get_results(&quot;SELECT * FROM $table WHERE status = &#039;active&#039; AND next_payment_date &lt;= NOW()&quot;) |  |
| 101 | 13 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 101 | 13 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |

## `includes/admin/menu.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 121 | 93 | WARNING | WordPress.WP.EnqueuedResourceParameters.MissingVersion | Resource version not set in call to wp_enqueue_script(). This means new versions of the script may not always be loaded due to browser caching. |  |
| 122 | 108 | WARNING | WordPress.WP.EnqueuedResourceParameters.MissingVersion | Resource version not set in call to wp_enqueue_script(). This means new versions of the script may not always be loaded due to browser caching. |  |
| 155 | 24 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 155 | 41 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 155 | 41 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_GET[&#039;page&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 155 | 41 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_GET[&#039;page&#039;] |  |
| 183 | 20 | ERROR | WordPress.WP.EnqueuedResources.NonEnqueuedScript | Scripts must be registered/enqueued via wp_enqueue_script() |  |

## `frontend/templates/payment-success.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 17 | 22 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 17 | 58 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 19 | 18 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 19 | 68 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |

## `frontend/templates/donor-dashboard.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 23 | 21 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;get_results($wpdb-&gt;prepare(&quot;SELECT * FROM {$table_donations} WHERE user_id = %d ORDER BY created_at DESC&quot;, $user_id))\n$table_donations assigned unsafely at line 21:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$donations assigned unsafely at line 23:\n $donations = $wpdb-&gt;get_results($wpdb-&gt;prepare(&quot;SELECT * FROM {$table_donations} WHERE user_id = %d ORDER BY created_at DESC&quot;, $user_id))\n$user_id assigned unsafely at line 13:\n $user_id = get_current_user_id() |  |
| 23 | 48 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_donations} at &quot;SELECT * FROM {$table_donations} WHERE user_id = %d ORDER BY created_at DESC&quot; |  |
| 117 | 22 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 117 | 29 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_subs used in $wpdb-&gt;get_results($wpdb-&gt;prepare(\n &quot;SELECT s.*, p.post_title as campaign_title \n FROM {$table_subs} s\n JOIN {$wpdb-&gt;posts} p ON s.campaign_id = p.ID\n WHERE s.user_id = %d \n ORDER BY s.created_at DESC&quot;,\n $user_id\n ))\n$table_subs assigned unsafely at line 115:\n $table_subs = $wpdb-&gt;prefix . &#039;wpd_subscriptions&#039;\n$subscriptions assigned unsafely at line 117:\n $subscriptions = $wpdb-&gt;get_results($wpdb-&gt;prepare(\n &quot;SELECT s.*, p.post_title as campaign_title \n FROM {$table_subs} s\n JOIN {$wpdb-&gt;posts} p ON s.campaign_id = p.ID\n WHERE s.user_id = %d \n ORDER BY s.created_at DESC&quot;,\n $user_id\n ))\n$user_id assigned unsafely at line 13:\n $user_id = get_current_user_id() |  |
| 119 | 1 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_subs} at FROM {$table_subs} s\n |  |

## `frontend/templates/profile.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 15 | 11 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |

## `includes/cpt.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 111 | 60 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |

## `includes/admin/dashboard-widget.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 32 | 24 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 32 | 24 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 32 | 31 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;get_var(&quot;SELECT SUM(amount) FROM $table_donations WHERE status = &#039;complete&#039;&quot;)\n$table_donations assigned unsafely at line 29:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$total_collected assigned unsafely at line 32:\n $total_collected = $wpdb-&gt;get_var(&quot;SELECT SUM(amount) FROM $table_donations WHERE status = &#039;complete&#039;&quot;) |  |
| 32 | 39 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table_donations at &quot;SELECT SUM(amount) FROM $table_donations WHERE status = &#039;complete&#039;&quot; |  |
| 35 | 21 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 35 | 21 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 35 | 28 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;get_var(&quot;SELECT COUNT(DISTINCT email) FROM $table_donations WHERE status = &#039;complete&#039;&quot;)\n$table_donations assigned unsafely at line 29:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$total_collected assigned unsafely at line 32:\n $total_collected = $wpdb-&gt;get_var(&quot;SELECT SUM(amount) FROM $table_donations WHERE status = &#039;complete&#039;&quot;) |  |
| 35 | 36 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table_donations at &quot;SELECT COUNT(DISTINCT email) FROM $table_donations WHERE status = &#039;complete&#039;&quot; |  |
| 41 | 15 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 41 | 15 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 41 | 22 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;get_results(&quot;SELECT * FROM $table_donations WHERE status = &#039;complete&#039; ORDER BY created_at DESC LIMIT 5&quot;)\n$table_donations assigned unsafely at line 29:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$total_collected assigned unsafely at line 32:\n $total_collected = $wpdb-&gt;get_var(&quot;SELECT SUM(amount) FROM $table_donations WHERE status = &#039;complete&#039;&quot;) |  |
| 41 | 34 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table_donations at &quot;SELECT * FROM $table_donations WHERE status = &#039;complete&#039; ORDER BY created_at DESC LIMIT 5&quot; |  |

## `includes/db.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 107 | 12 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 107 | 12 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 107 | 16 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;get_results(&quot;SHOW COLUMNS FROM $table_donations LIKE &#039;subscription_id&#039;&quot;)\n$table_donations assigned unsafely at line 14:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$table_meta assigned unsafely at line 15:\n $table_meta = $wpdb-&gt;prefix . &#039;wpd_campaign_meta&#039;\n$sql_donations assigned unsafely at line 18:\n $sql_donations = &quot;CREATE TABLE $table_donations (\n\t\tid bigint(20) NOT NULL AUTO_INCREMENT,\n\t\tcampaign_id bigint(20) NOT NULL,\n\t\tuser_id bigint(20) NULL,\n\t\tname varchar(100) NOT NULL,\n\t\temail varchar(100) NOT NULL,\n\t\tphone varchar(20) NULL,\n\t\tamount decimal(12,2) NOT NULL,\n\t\tcurrency varchar(3) DEFAULT &#039;IDR&#039;,\n\t\tpayment_method varchar(50) NOT NULL,\n\t\tstatus enum(&#039;pending&#039;,&#039;processing&#039;,&#039;complete&#039;,&#039;failed&#039;,&#039;refunded&#039;) DEFAULT &#039;pending&#039;,\n\t\tgateway varchar(50) NULL,\n\t\tgateway_txn_id varchar(100) NULL,\n\t\tmetadata longtext NULL,\n\t\tnote text NULL,\n\t\tis_anonymous tinyint(1) DEFAULT 0,\n\t\tfundraiser_id bigint(20) DEFAULT 0,\n\t\tcreated_at datetime DEFAULT CURRENT_TIMESTAMP,\n\t\tupdated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n\t\tPRIMARY KEY (id),\n\t\tKEY campaign_id (campaign_id),\n\t\tKEY user_id (user_id),\n\t\tKEY status (status),\n\t\tKEY created_at (created_at)\n\t) $charset_collate;&quot; |  |
| 107 | 29 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table_donations at &quot;SHOW COLUMNS FROM $table_donations LIKE &#039;subscription_id&#039;&quot; |  |
| 108 | 10 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;query(&quot;ALTER TABLE $table_donations ADD COLUMN subscription_id bigint(20) DEFAULT 0 AFTER fundraiser_id&quot;)\n$table_donations assigned unsafely at line 14:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$table_meta assigned unsafely at line 15:\n $table_meta = $wpdb-&gt;prefix . &#039;wpd_campaign_meta&#039;\n$sql_donations assigned unsafely at line 18:\n $sql_donations = &quot;CREATE TABLE $table_donations (\n\t\tid bigint(20) NOT NULL AUTO_INCREMENT,\n\t\tcampaign_id bigint(20) NOT NULL,\n\t\tuser_id bigint(20) NULL,\n\t\tname varchar(100) NOT NULL,\n\t\temail varchar(100) NOT NULL,\n\t\tphone varchar(20) NULL,\n\t\tamount decimal(12,2) NOT NULL,\n\t\tcurrency varchar(3) DEFAULT &#039;IDR&#039;,\n\t\tpayment_method varchar(50) NOT NULL,\n\t\tstatus enum(&#039;pending&#039;,&#039;processing&#039;,&#039;complete&#039;,&#039;failed&#039;,&#039;refunded&#039;) DEFAULT &#039;pending&#039;,\n\t\tgateway varchar(50) NULL,\n\t\tgateway_txn_id varchar(100) NULL,\n\t\tmetadata longtext NULL,\n\t\tnote text NULL,\n\t\tis_anonymous tinyint(1) DEFAULT 0,\n\t\tfundraiser_id bigint(20) DEFAULT 0,\n\t\tcreated_at datetime DEFAULT CURRENT_TIMESTAMP,\n\t\tupdated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n\t\tPRIMARY KEY (id),\n\t\tKEY campaign_id (campaign_id),\n\t\tKEY user_id (user_id),\n\t\tKEY status (status),\n\t\tKEY created_at (created_at)\n\t) $charset_collate;&quot; |  |
| 108 | 17 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table_donations at &quot;ALTER TABLE $table_donations ADD COLUMN subscription_id bigint(20) DEFAULT 0 AFTER fundraiser_id&quot; |  |
| 108 | 23 | WARNING | WordPress.DB.DirectDatabaseQuery.SchemaChange | Attempting a database schema change is discouraged. |  |

## `includes/gateways/midtrans.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 74 | 21 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 122 | 17 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 122 | 17 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 220 | 31 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 220 | 31 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 220 | 38 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;get_var($wpdb-&gt;prepare( &quot;SELECT status FROM $table_donations WHERE id = %d&quot;, $donation_id ))\n$table_donations assigned unsafely at line 174:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$settings assigned unsafely at line 175:\n $settings = get_option( &#039;wpd_settings_midtrans&#039;, [] ) |  |
| 220 | 63 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table_donations at &quot;SELECT status FROM $table_donations WHERE id = %d&quot; |  |
| 223 | 17 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 223 | 17 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 234 | 36 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 234 | 36 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 234 | 43 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;get_var($wpdb-&gt;prepare( &quot;SELECT campaign_id FROM $table_donations WHERE id = %d&quot;, $donation_id ))\n$table_donations assigned unsafely at line 174:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$settings assigned unsafely at line 175:\n $settings = get_option( &#039;wpd_settings_midtrans&#039;, [] ) |  |
| 234 | 68 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table_donations at &quot;SELECT campaign_id FROM $table_donations WHERE id = %d&quot; |  |
| 237 | 34 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 237 | 34 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 237 | 41 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;get_var($wpdb-&gt;prepare( &quot;SELECT SUM(amount) FROM $table_donations WHERE campaign_id = %d AND status = &#039;complete&#039;&quot;, $campaign_id ))\n$table_donations assigned unsafely at line 174:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$settings assigned unsafely at line 175:\n $settings = get_option( &#039;wpd_settings_midtrans&#039;, [] ) |  |
| 237 | 66 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table_donations at &quot;SELECT SUM(amount) FROM $table_donations WHERE campaign_id = %d AND status = &#039;complete&#039;&quot; |  |
| 241 | 37 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 241 | 37 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 241 | 44 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;get_row($wpdb-&gt;prepare( &quot;SELECT * FROM $table_donations WHERE id = %d&quot;, $donation_id ))\n$table_donations assigned unsafely at line 174:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$settings assigned unsafely at line 175:\n $settings = get_option( &#039;wpd_settings_midtrans&#039;, [] ) |  |
| 241 | 69 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table_donations at &quot;SELECT * FROM $table_donations WHERE id = %d&quot; |  |

## `includes/api/fundraisers-controller.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 76 | 20 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 76 | 20 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 76 | 21 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table used in $wpdb-&gt;get_results($wpdb-&gt;prepare( &quot;SELECT * FROM $table WHERE user_id = %d&quot;, $user_id ))\n$table assigned unsafely at line 75:\n $table = $wpdb-&gt;prefix . &#039;wpd_fundraisers&#039;\n$results assigned unsafely at line 76:\n $results = $wpdb-&gt;get_results( $wpdb-&gt;prepare( &quot;SELECT * FROM $table WHERE user_id = %d&quot;, $user_id ) )\n$user_id assigned unsafely at line 74:\n $user_id = get_current_user_id() |  |
| 76 | 50 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table at &quot;SELECT * FROM $table WHERE user_id = %d&quot; |  |
| 86 | 20 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 86 | 20 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 86 | 21 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table used in $wpdb-&gt;get_results(&quot;SELECT * FROM $table ORDER BY created_at DESC LIMIT 50&quot;)\n$table assigned unsafely at line 85:\n $table = $wpdb-&gt;prefix . &#039;wpd_fundraisers&#039;\n$table assigned unsafely at line 75:\n $table = $wpdb-&gt;prefix . &#039;wpd_fundraisers&#039;\n$results assigned unsafely at line 86:\n $results = $wpdb-&gt;get_results( &quot;SELECT * FROM $table ORDER BY created_at DESC LIMIT 50&quot; )\n$user_id assigned unsafely at line 74:\n $user_id = get_current_user_id() |  |
| 86 | 34 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table at &quot;SELECT * FROM $table ORDER BY created_at DESC LIMIT 50&quot; |  |

## `includes/services/email.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 33 | 21 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 33 | 22 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;get_row($wpdb-&gt;prepare(&quot;SELECT * FROM $table_donations WHERE id = %d&quot;, $donation_id))\n$table_donations assigned unsafely at line 31:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$donation assigned unsafely at line 33:\n $donation = $wpdb-&gt;get_row($wpdb-&gt;prepare(&quot;SELECT * FROM $table_donations WHERE id = %d&quot;, $donation_id))\n$donation_id used without escaping. |  |
| 33 | 45 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table_donations at &quot;SELECT * FROM $table_donations WHERE id = %d&quot; |  |

## `includes/services/fundraiser.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 26 | 21 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 26 | 21 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 27 | 4 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $this-&gt;table_name at &quot;SELECT * FROM $this-&gt;table_name WHERE user_id = %d AND campaign_id = %d&quot; |  |
| 46 | 9 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 61 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 61 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 62 | 4 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $this-&gt;table_name at &quot;SELECT * FROM $this-&gt;table_name WHERE referral_code = %s&quot; |  |
| 72 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 72 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 73 | 4 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $this-&gt;table_name at &quot;SELECT * FROM $this-&gt;table_name WHERE id = %d&quot; |  |
| 83 | 9 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 83 | 9 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 84 | 4 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $this-&gt;table_name at &quot;UPDATE $this-&gt;table_name \n |  |
| 97 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 97 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 99 | 1 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $this-&gt;table_name at \t\t\t FROM $this-&gt;table_name f\n |  |
| 118 | 17 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_SERVER[&#039;REMOTE_ADDR&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 118 | 17 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_SERVER[&#039;REMOTE_ADDR&#039;] |  |
| 119 | 17 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_SERVER[&#039;HTTP_USER_AGENT&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 119 | 17 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_SERVER[&#039;HTTP_USER_AGENT&#039;] |  |
| 121 | 9 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |

## `includes/services/donation.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 17 | 29 | WARNING | WordPress.Security.NonceVerification.Missing | Processing form data without nonce verification. |  |
| 17 | 29 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;wpd_action&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 17 | 29 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;wpd_action&#039;] |  |
| 18 | 52 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotValidated | Detected usage of a possibly undefined superglobal array index: $_POST[&#039;wpd_donate_nonce&#039;]. Check that the array index exists before using it. |  |
| 18 | 52 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;wpd_donate_nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 18 | 52 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;wpd_donate_nonce&#039;] |  |
| 22 | 64 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;wpd_donate_nonce&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 22 | 64 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;wpd_donate_nonce&#039;] |  |
| 32 | 82 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;amount&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 34 | 63 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;donor_name&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 35 | 60 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;donor_email&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 36 | 65 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;donor_phone&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 37 | 67 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;donor_note&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 49 | 73 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;payment_method&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 73 | 59 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;qurban_package&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 79 | 70 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;qurban_names&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 117 | 71 | WARNING | WordPress.Security.ValidatedSanitizedInput.MissingUnslash | $_POST[&#039;recurring_interval&#039;] not unslashed before sanitization. Use wp_unslash() or similar |  |
| 204 | 14 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 204 | 14 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 204 | 21 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table used in $wpdb-&gt;get_var($wpdb-&gt;prepare(&quot;SELECT SUM(amount) FROM $table WHERE campaign_id = %d AND status = &#039;complete&#039;&quot;, $campaign_id))\n$table assigned unsafely at line 196:\n $table = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$total assigned unsafely at line 204:\n $total = $wpdb-&gt;get_var($wpdb-&gt;prepare(&quot;SELECT SUM(amount) FROM $table WHERE campaign_id = %d AND status = &#039;complete&#039;&quot;, $campaign_id))\n$campaign_id used without escaping. |  |
| 204 | 44 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table at &quot;SELECT SUM(amount) FROM $table WHERE campaign_id = %d AND status = &#039;complete&#039;&quot; |  |
| 207 | 20 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 207 | 20 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 207 | 27 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table used in $wpdb-&gt;get_var($wpdb-&gt;prepare(&quot;SELECT COUNT(DISTINCT email) FROM $table WHERE campaign_id = %d AND status = &#039;complete&#039;&quot;, $campaign_id))\n$table assigned unsafely at line 196:\n $table = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$total assigned unsafely at line 204:\n $total = $wpdb-&gt;get_var($wpdb-&gt;prepare(&quot;SELECT SUM(amount) FROM $table WHERE campaign_id = %d AND status = &#039;complete&#039;&quot;, $campaign_id))\n$campaign_id used without escaping. |  |
| 207 | 50 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table at &quot;SELECT COUNT(DISTINCT email) FROM $table WHERE campaign_id = %d AND status = &#039;complete&#039;&quot; |  |

## `uninstall.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 34 | 22 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable $table at &quot;DROP TABLE IF EXISTS $table&quot; |  |
| 53 | 20 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 53 | 20 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |

## `includes/api/campaigns-controller.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 100 | 17 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
