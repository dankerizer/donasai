# Plugin Check Report

**Plugin:** Donasai - Platform Donasi & Penggalangan Dana
**Generated at:** 2026-02-04 15:14:32


## `includes/api/donations-controller.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 67 | 20 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 112 | 24 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 120 | 22 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 147 | 28 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 147 | 35 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;get_var($wpdb-&gt;prepare(&quot;SELECT SUM(amount) FROM {$table_donations} WHERE status = %s&quot;, &#039;complete&#039;))\n$table_donations assigned unsafely at line 146:\n $table_donations = $wpdb-&gt;prefix . &#039;donasai_donations&#039;\n$total_collected assigned unsafely at line 147:\n $total_collected = $wpdb-&gt;get_var($wpdb-&gt;prepare(&quot;SELECT SUM(amount) FROM {$table_donations} WHERE status = %s&quot;, &#039;complete&#039;)) |  |
| 147 | 58 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_donations} at &quot;SELECT SUM(amount) FROM {$table_donations} WHERE status = %s&quot; |  |
| 148 | 25 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 148 | 32 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;get_var($wpdb-&gt;prepare(&quot;SELECT COUNT(DISTINCT email) FROM {$table_donations} WHERE status = %s&quot;, &#039;complete&#039;))\n$table_donations assigned unsafely at line 146:\n $table_donations = $wpdb-&gt;prefix . &#039;donasai_donations&#039;\n$total_collected assigned unsafely at line 147:\n $total_collected = $wpdb-&gt;get_var($wpdb-&gt;prepare(&quot;SELECT SUM(amount) FROM {$table_donations} WHERE status = %s&quot;, &#039;complete&#039;)) |  |
| 148 | 55 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_donations} at &quot;SELECT COUNT(DISTINCT email) FROM {$table_donations} WHERE status = %s&quot; |  |
| 170 | 29 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 175 | 26 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 191 | 9 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 198 | 22 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 198 | 29 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;get_var($wpdb-&gt;prepare(&quot;\n SELECT COUNT(*) FROM (\n SELECT email FROM {$table_donations} \n WHERE status = %s \n GROUP BY email \n HAVING COUNT(id) &gt; 1\n ) as repeaters\n &quot;, &#039;complete&#039;))\n$table_donations assigned unsafely at line 197:\n $table_donations = $wpdb-&gt;prefix . &#039;donasai_donations&#039;\n$table_donations assigned unsafely at line 146:\n $table_donations = $wpdb-&gt;prefix . &#039;donasai_donations&#039;\n$repeat_donors assigned unsafely at line 198:\n $repeat_donors = $wpdb-&gt;get_var($wpdb-&gt;prepare(&quot;\n SELECT COUNT(*) FROM (\n SELECT email FROM {$table_donations} \n WHERE status = %s \n GROUP BY email \n HAVING COUNT(id) &gt; 1\n ) as repeaters\n &quot;, &#039;complete&#039;))\n$total_collected assigned unsafely at line 159:\n $total_collected = $cached_stats[&#039;total_collected&#039;]\n$cached_stats[&#039;total_collected&#039;] used without escaping. |  |
| 200 | 1 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_donations} at SELECT email FROM {$table_donations} \n |  |
| 312 | 33 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $sql |  |
| 315 | 33 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_name} at &quot;SELECT * FROM {$table_name} WHERE 1=1 ORDER BY created_at DESC LIMIT %d&quot; |  |
| 318 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 318 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 318 | 23 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $query used in $wpdb-&gt;get_results($query)\n$query assigned unsafely at line 315:\n $query = $wpdb-&gt;prepare(&quot;SELECT * FROM {$table_name} WHERE 1=1 ORDER BY created_at DESC LIMIT %d&quot;, 10000)\n$table_name assigned unsafely at line 304:\n $table_name = $wpdb-&gt;prefix . &#039;donasai_donations&#039;\n$where_sql assigned unsafely at line 305:\n $where_sql = $query_parts[&#039;where&#039;]\n$query_parts[&#039;where&#039;] used without escaping. |  |
| 392 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 392 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 409 | 24 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 409 | 24 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 418 | 20 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 418 | 20 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 471 | 39 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $count_sql |  |
| 475 | 26 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 475 | 26 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 475 | 33 | ERROR | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $count_query used in $wpdb->get_var($count_query)\n$count_query assigned unsafely at line 473:\n $count_query = "SELECT COUNT(*) FROM {$table_name}"\n$table_name assigned unsafely at line 464:\n $table_name = $wpdb->prefix . 'donasai_donations'\n$where_sql assigned unsafely at line 465:\n $where_sql = $query_parts['where']\n$query_parts['where'] used without escaping. |  |
| 475 | 41 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $count_query |  |
| 486 | 29 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $data_sql |  |
| 487 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 487 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 487 | 23 | ERROR | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $query used in $wpdb->get_results($query)\n$query assigned unsafely at line 486:\n $query = $wpdb->prepare($data_sql, $data_args)\n$data_sql assigned unsafely at line 479:\n $data_sql = "SELECT * FROM {$table_name} WHERE {$where_sql} ORDER BY created_at DESC LIMIT %d OFFSET %d"\n$table_name assigned unsafely at line 464:\n $table_name = $wpdb->prefix . 'donasai_donations'\n$where_sql assigned unsafely at line 465:\n $where_sql = $query_parts['where']\n$query_parts['where'] used without escaping. |  |
| 487 | 35 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $query |  |

## `includes/functions-frontend.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 19 | 31 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 421 | 20 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 422 | 13 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table} at &quot;SELECT * FROM {$table} WHERE campaign_id = %d AND status = &#039;complete&#039; ORDER BY created_at DESC LIMIT %d&quot; |  |
| 444 | 24 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 445 | 13 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table} at &quot;SELECT COUNT(id) FROM {$table} WHERE campaign_id = %d AND status = &#039;complete&#039;&quot; |  |
| 533 | 15 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 534 | 52 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 566 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 566 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 568 | 1 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_fundraisers} at FROM {$table_fundraisers} f\n |  |
| 597 | 36 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 597 | 66 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_logs} at &quot;SELECT COUNT(id) FROM {$table_logs} WHERE fundraiser_id = %d&quot; |  |
| 700 | 25 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 700 | 55 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_donations} at &quot;SELECT amount FROM {$table_donations} WHERE id = %d&quot; |  |
| 719 | 29 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 719 | 59 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_donations} at &quot;SELECT * FROM {$table_donations} WHERE id = %d&quot; |  |
| 729 | 68 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_FILES[&#039;proof_file&#039;] |  |
| 756 | 29 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 756 | 29 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 801 | 15 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 802 | 31 | WARNING | WordPress.Security.NonceVerification.Recommended | Processing form data without nonce verification. |  |
| 805 | 47 | ERROR | PluginCheck.CodeAnalysis.EnqueuedResourceOffloading.OffloadedContent | Found call to wp_enqueue_script() with external resource. Offloading scripts to your servers or any remote service is disallowed. |  |
| 808 | 144 | WARNING | WordPress.WP.EnqueuedResourceParameters.MissingVersion | Resource version not set in call to wp_enqueue_style(). This means new versions of the style may not always be loaded due to browser caching. |  |
| 895 | 19 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 895 | 19 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 895 | 26 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table used in $wpdb-&gt;get_var($wpdb-&gt;prepare(&quot;SELECT status FROM {$table} WHERE id = %d&quot;, $donation_id))\n$table assigned unsafely at line 894:\n $table = $wpdb-&gt;prefix . &#039;donasai_donations&#039;\n$status assigned unsafely at line 895:\n $status = $wpdb-&gt;get_var($wpdb-&gt;prepare(&quot;SELECT status FROM {$table} WHERE id = %d&quot;, $donation_id)) |  |
| 895 | 49 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table} at &quot;SELECT status FROM {$table} WHERE id = %d&quot; |  |

## `admin-app/.gitignore`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | WARNING | hidden_files | Hidden files are not permitted. |  |

## `.distignore`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | WARNING | hidden_files | Hidden files are not permitted. |  |

## `GEMINI.md`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 0 | 0 | WARNING | unexpected_markdown_file | Unexpected markdown file "GEMINI.md" detected in plugin root. Only specific markdown files are expected in production plugins. |  |

## `frontend/templates/receipt.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 9 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$donation_id&quot;. |  |
| 10 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$donation&quot;. |  |
| 15 | 9 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$donation&quot;. |  |
| 18 | 9 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$table&quot;. |  |
| 19 | 9 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$donation&quot;. |  |
| 19 | 21 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 19 | 51 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table} at &quot;SELECT * FROM {$table} WHERE id = %d&quot; |  |
| 28 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$is_valid_access&quot;. |  |
| 32 | 5 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$token_seed&quot;. |  |
| 33 | 5 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$expected_token&quot;. |  |
| 34 | 5 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$provided_token&quot;. |  |
| 37 | 9 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$is_valid_access&quot;. |  |
| 43 | 5 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$nonce&quot;. |  |
| 45 | 9 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$is_valid_access&quot;. |  |
| 51 | 5 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$is_valid_access&quot;. |  |
| 56 | 5 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$is_valid_access&quot;. |  |
| 64 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$campaign_title&quot;. |  |
| 65 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$donor_name&quot;. |  |
| 66 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$donor_email&quot;. |  |
| 67 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$donor_phone&quot;. |  |
| 68 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$donation_date&quot;. |  |
| 69 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$amount&quot;. |  |
| 70 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$payment_method&quot;. |  |
| 73 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$settings&quot;. |  |
| 74 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$org_name&quot;. |  |
| 75 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$org_address&quot;. |  |
| 76 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$org_email&quot;. |  |
| 77 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$org_phone&quot;. |  |
| 78 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$org_logo&quot;. |  |
| 82 | 5 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$custom_logo_id&quot;. |  |
| 83 | 5 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$org_logo&quot;. |  |
| 87 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$is_pro&quot;. |  |
| 90 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$appearance&quot;. |  |
| 91 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$dark_mode&quot;. |  |
| 112 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$brand_color&quot;. |  |
| 114 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$brand_50&quot;. |  |
| 115 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$brand_100&quot;. |  |
| 116 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$brand_500&quot;. |  |
| 117 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$brand_600&quot;. |  |
| 118 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$brand_700&quot;. |  |
| 119 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$brand_800&quot;. |  |
| 120 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$brand_900&quot;. |  |

## `frontend/templates/donor-dashboard.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 13 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$user_id&quot;. |  |
| 22 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$cache_key&quot;. |  |
| 23 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$donations&quot;. |  |
| 26 | 5 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$table_donations&quot;. |  |
| 27 | 5 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$donations&quot;. |  |
| 27 | 18 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 27 | 18 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 27 | 52 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_donations} at &quot;SELECT * FROM {$table_donations} WHERE user_id = %d ORDER BY created_at DESC&quot; |  |
| 75 | 50 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$donation&quot;. |  |
| 76 | 25 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$campaign_title&quot;. |  |
| 121 | 5 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$cache_key_subs&quot;. |  |
| 122 | 5 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$subscriptions&quot;. |  |
| 125 | 9 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$table_subs&quot;. |  |
| 126 | 9 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$table_posts&quot;. |  |
| 127 | 9 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$subscriptions&quot;. |  |
| 127 | 26 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 127 | 26 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 129 | 1 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_subs} at FROM {$table_subs} s\n |  |
| 130 | 1 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_posts} at JOIN {$table_posts} p ON s.campaign_id = p.ID\n |  |
| 173 | 54 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$sub&quot;. |  |

## `frontend/templates/donation-summary.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 11 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$thankyou_slug&quot;. |  |
| 12 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$donation_id&quot;. |  |
| 20 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$table_donations&quot;. |  |
| 21 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$donation&quot;. |  |
| 21 | 13 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 21 | 13 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 21 | 43 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_donations} at &quot;SELECT * FROM {$table_donations} WHERE id = %d&quot; |  |
| 27 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$campaign_id&quot;. |  |
| 29 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$amount&quot;. |  |
| 32 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$gateway_id&quot;. |  |
| 35 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$gateway&quot;. |  |
| 38 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$primary_color&quot;. |  |
| 39 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$button_color&quot;. |  |
| 156 | 21 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$settings_gen&quot;. |  |
| 157 | 21 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$conf_page_id&quot;. |  |
| 158 | 21 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$conf_url&quot;. |  |
| 161 | 21 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$conf_url_final&quot;. |  |
| 162 | 21 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$raw_phone&quot;. |  |
| 163 | 21 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$fmt_amount&quot;. |  |
| 191 | 13 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$gen_settings&quot;. |  |
| 192 | 13 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$is_branding_removed&quot;. |  |

## `includes/metabox.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 207 | 29 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;donasai_whatsapp_settings&#039;] |  |
| 224 | 29 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;donasai_campaign_banks&#039;] |  |

## `includes/admin/dashboard-widget.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 39 | 37 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 42 | 34 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 42 | 64 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_donations} at &quot;SELECT COUNT(DISTINCT email) FROM {$table_donations} WHERE status = %s&quot; |  |
| 48 | 28 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 48 | 62 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_donations} at &quot;SELECT * FROM {$table_donations} WHERE status = %s ORDER BY created_at DESC LIMIT %d&quot; |  |

## `includes/gateways/manual.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 64 | 21 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 132 | 26 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 132 | 56 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table} at &quot;SELECT * FROM {$table} WHERE id = %d&quot; |  |

## `includes/api/campaigns-controller.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 110 | 17 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 179 | 20 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 180 | 4 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table} at &quot;SELECT * FROM {$table} WHERE campaign_id = %d AND status = &#039;complete&#039; ORDER BY created_at DESC LIMIT %d OFFSET %d&quot; |  |
| 189 | 20 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 190 | 3 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table} at &quot;SELECT COUNT(id) FROM {$table} WHERE campaign_id = %d AND status = &#039;complete&#039;&quot; |  |

## `includes/services/fundraiser.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 29 | 21 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 29 | 21 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 50 | 9 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 70 | 22 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 89 | 22 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 104 | 9 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 105 | 4 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$this-&gt;table_name} at &quot;UPDATE {$this-&gt;table_name} \n |  |
| 127 | 24 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 129 | 1 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$this-&gt;table_name} at \t\t\t\t FROM {$this-&gt;table_name} f\n |  |
| 156 | 9 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |

## `includes/services/donation.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 199 | 14 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 199 | 44 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table} at &quot;SELECT SUM(amount) FROM {$table} WHERE campaign_id = %d AND status = &#039;complete&#039;&quot; |  |
| 202 | 20 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 202 | 50 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table} at &quot;SELECT COUNT(DISTINCT email) FROM {$table} WHERE campaign_id = %d AND status = &#039;complete&#039;&quot; |  |
| 224 | 51 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table} at &quot;SELECT * FROM {$table} WHERE id = %d&quot; |  |

## `uninstall.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 12 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$general_settings&quot;. |  |
| 24 | 5 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$tables&quot;. |  |
| 32 | 25 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$table&quot;. |  |
| 33 | 9 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 33 | 9 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 33 | 22 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table} at &quot;DROP TABLE IF EXISTS {$table}&quot; |  |
| 33 | 22 | WARNING | WordPress.DB.DirectDatabaseQuery.SchemaChange | Attempting a database schema change is discouraged. |  |
| 41 | 5 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$options&quot;. |  |
| 52 | 5 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$pro_options&quot;. |  |
| 52 | 20 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 52 | 20 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 54 | 9 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$options&quot;. |  |
| 57 | 26 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$option&quot;. |  |

## `includes/db.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 110 | 10 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |

## `includes/api/fundraisers-controller.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 97 | 24 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 116 | 24 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |

## `includes/services/email.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 39 | 29 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |

## `includes/services/subscription.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 34 | 21 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 54 | 30 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 74 | 20 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 95 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 95 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 107 | 13 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 107 | 13 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |

## `includes/admin/campaign-columns.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 191 | 13 | WARNING | WordPress.DB.SlowDBQuery.slow_db_query_meta_key | Detected usage of meta_key, possible slow query. |  |

## `includes/admin/menu.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 121 | 97 | WARNING | WordPress.WP.EnqueuedResourceParameters.MissingVersion | Resource version not set in call to wp_enqueue_script(). This means new versions of the script may not always be loaded due to browser caching. |  |
| 122 | 116 | WARNING | WordPress.WP.EnqueuedResourceParameters.MissingVersion | Resource version not set in call to wp_enqueue_script(). This means new versions of the script may not always be loaded due to browser caching. |  |

## `frontend/templates/campaign-list.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 12 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$settings_app&quot;. |  |
| 13 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$primary_color&quot;. |  |
| 14 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$button_color&quot;. |  |
| 15 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$font_family&quot;. |  |
| 16 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$border_radius&quot;. |  |
| 19 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$args&quot;. |  |
| 24 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$query&quot;. |  |
| 37 | 17 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$campaign_id&quot;. |  |
| 38 | 17 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$progress&quot;. |  |
| 42 | 17 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$is_unlimited&quot;. |  |

## `frontend/templates/confirmation-form.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 134 | 5 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$gen_settings&quot;. |  |
| 135 | 5 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$is_branding_removed&quot;. |  |

## `frontend/templates/payment-success.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 16 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$campaign_id&quot;. |  |
| 17 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$donation_id&quot;. |  |
| 19 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$gateway&quot;. |  |
| 22 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$nonce&quot;. |  |
| 40 | 5 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$donation_id&quot;. |  |
| 90 | 21 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$receipt_url&quot;. |  |
| 116 | 5 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$gen_settings&quot;. |  |
| 117 | 5 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$is_branding_removed&quot;. |  |

## `frontend/templates/payment.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 13 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$campaign_id&quot;. |  |

## `frontend/templates/donation-form.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 9 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$campaign_id&quot;. |  |
| 11 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$thumbnail&quot;. |  |
| 13 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$packages&quot;. |  |
| 14 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$packages&quot;. |  |
| 18 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$default_name&quot;. |  |
| 19 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$default_email&quot;. |  |
| 22 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$settings&quot;. |  |
| 23 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$min_amount&quot;. |  |
| 24 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$presets&quot;. |  |
| 25 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$presets&quot;. |  |
| 26 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$preset_emoji&quot;. |  |
| 29 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$settings_app&quot;. |  |
| 31 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$is_pro&quot;. |  |
| 32 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$dark_mode&quot;. |  |
| 34 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$donation_layout&quot;. |  |
| 36 | 5 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$donation_layout&quot;. |  |
| 39 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$body_classes&quot;. |  |
| 40 | 17 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$body_classes&quot;. |  |
| 41 | 35 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$body_classes&quot;. |  |
| 117 | 57 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$pkg&quot;. |  |
| 142 | 56 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$val&quot;. |  |
| 220 | 25 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$manual_gateway&quot;. |  |
| 221 | 25 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$active_banks&quot;. |  |
| 224 | 29 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$first&quot;. |  |
| 225 | 55 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$bank&quot;. |  |
| 227 | 33 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$p_val&quot;. |  |
| 243 | 33 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$first&quot;. |  |
| 284 | 21 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$fee_settings&quot;. |  |
| 285 | 21 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$fee_enabled&quot;. |  |
| 286 | 21 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$fee_default_checked&quot;. |  |
| 339 | 13 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$gen_settings&quot;. |  |
| 340 | 13 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$is_branding_removed&quot;. |  |

## `frontend/templates/campaign-single.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 10 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$payment_slug&quot;. |  |
| 49 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$campaign_id&quot;. |  |
| 50 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$is_verified&quot;. |  |
| 51 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$progress&quot;. |  |
| 52 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$settings_app&quot;. |  |
| 53 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$sidebar_limit_init&quot;. |  |
| 54 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$donors&quot;. |  |
| 55 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$total_donors&quot;. |  |
| 58 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$settings_app&quot;. |  |
| 59 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$container_width&quot;. |  |
| 60 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$border_radius&quot;. |  |
| 61 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$layout_mode&quot;. |  |
| 62 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$primary_color&quot;. |  |
| 63 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$button_color&quot;. |  |
| 64 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$sidebar_limit&quot;. |  |
| 65 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$per_page_limit&quot;. |  |
| 69 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$is_pro&quot;. |  |
| 72 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$hero_style&quot;. |  |
| 74 | 5 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$hero_style&quot;. |  |
| 79 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$show_countdown&quot;. |  |
| 80 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$show_prayer_tab&quot;. |  |
| 81 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$show_updates_tab&quot;. |  |
| 82 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$show_donor_list&quot;. |  |
| 83 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$show_leaderboard&quot;. |  |
| 87 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$debug_active_plugins&quot;. |  |
| 88 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$debug_in_array&quot;. |  |
| 89 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$debug_license&quot;. |  |
| 90 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$debug_func_exists&quot;. |  |
| 94 | 5 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$show_countdown&quot;. |  |
| 95 | 5 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$show_prayer_tab&quot;. |  |
| 96 | 5 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$show_updates_tab&quot;. |  |
| 248 | 21 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$donors_with_notes&quot;. |  |
| 257 | 66 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$donor&quot;. |  |
| 258 | 33 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$name&quot;. |  |
| 259 | 33 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$time&quot;. |  |
| 260 | 33 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$initial&quot;. |  |
| 296 | 55 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$donor&quot;. |  |
| 297 | 33 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$name&quot;. |  |
| 298 | 33 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$time&quot;. |  |
| 299 | 33 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$initial&quot;. |  |
| 350 | 17 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$donate_rest_url&quot;. |  |
| 352 | 17 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$campaign_js&quot;. |  |
| 422 | 21 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$deadline_str&quot;. |  |
| 424 | 25 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$deadline&quot;. |  |
| 425 | 25 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$now&quot;. |  |
| 426 | 25 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$diff&quot;. |  |
| 429 | 29 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$days&quot;. |  |
| 430 | 29 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$hours&quot;. |  |
| 431 | 29 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$minutes&quot;. |  |
| 488 | 25 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$donate_link&quot;. |  |
| 490 | 25 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$donate_link&quot;. |  |
| 517 | 17 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$org_settings&quot;. |  |
| 518 | 17 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$fundraiser_name&quot;. |  |
| 519 | 17 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$fundraiser_avatar&quot;. |  |
| 546 | 21 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$fundraiser_service&quot;. |  |
| 547 | 21 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$leaderboard&quot;. |  |
| 559 | 29 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$rank&quot;. |  |
| 560 | 54 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$fr&quot;. |  |
| 561 | 33 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$fr_name&quot;. |  |
| 562 | 33 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$fr_initial&quot;. |  |
| 563 | 33 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$fr_total&quot;. |  |
| 564 | 33 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$rank_color&quot;. |  |
| 607 | 59 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$donor&quot;. |  |
| 608 | 37 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$name&quot;. |  |
| 609 | 37 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$time&quot;. |  |
| 664 | 5 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$payment_slug&quot;. |  |
| 665 | 5 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$donate_link_sticky&quot;. |  |

## `frontend/templates/profile.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 11 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$phone&quot;. |  |
| 14 | 1 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$message&quot;. |  |
| 16 | 5 | WARNING | WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound | Global variables defined by a theme/plugin should start with the theme/plugin prefix. Found: &quot;$message&quot;. |  |
