# Plugin Check Report

**Plugin:** Donasai - Platform Donasi & Penggalangan Dana
**Generated at:** 2026-01-15 02:36:57


## `includes/api/donations-controller.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 67 | 20 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 112 | 24 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 120 | 22 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 146 | 28 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 147 | 25 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 169 | 29 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 174 | 26 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 190 | 9 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 196 | 22 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 311 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 311 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 311 | 23 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $query used in $wpdb-&gt;get_results($query)\n$query assigned unsafely at line 306:\n $query = $wpdb-&gt;prepare($sql, $query_parts[&#039;args&#039;])\n$sql assigned unsafely at line 305:\n $sql = &quot;SELECT * FROM {$table_name} WHERE &quot; . $query_parts[&#039;where&#039;] . &quot; ORDER BY created_at DESC&quot;\n$table_name assigned unsafely at line 304:\n $table_name = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$query_parts[&#039;where&#039;] used without escaping. |  |
| 381 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 381 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 398 | 24 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 398 | 24 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 407 | 20 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 407 | 20 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 460 | 94 | WARNING | WordPress.DB.PreparedSQLPlaceholders.UnfinishedPrepare | Replacement variables found, but no valid placeholders found in the query. |  |
| 464 | 26 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 464 | 26 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 464 | 33 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $count_query used in $wpdb-&gt;get_var($count_query)\n$count_query assigned unsafely at line 462:\n $count_query = &quot;SELECT COUNT(*) FROM {$table_name}&quot;\n$table_name assigned unsafely at line 453:\n $table_name = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$where_sql assigned unsafely at line 454:\n $where_sql = $query_parts[&#039;where&#039;]\n$query_parts[&#039;where&#039;] used without escaping. |  |
| 474 | 18 | WARNING | WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber | Incorrect number of replacements passed to $wpdb-&gt;prepare(). Found 1 replacement parameters, expected 2. |  |
| 475 | 13 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_name} at &quot;SELECT * FROM {$table_name} WHERE &quot; |  |
| 475 | 52 | ERROR | WordPress.DB.PreparedSQL.NotPrepared | Use placeholders and $wpdb->prepare(); found $where_sql |  |
| 480 | 13 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_name} at &quot;SELECT * FROM {$table_name} ORDER BY created_at DESC LIMIT %d OFFSET %d&quot; |  |
| 486 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 486 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 486 | 23 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $query used in $wpdb-&gt;get_results($query)\n$query assigned unsafely at line 479:\n $query = $wpdb-&gt;prepare(\n &quot;SELECT * FROM {$table_name} ORDER BY created_at DESC LIMIT %d OFFSET %d&quot;,\n $per_page,\n $offset\n )\n$table_name assigned unsafely at line 453:\n $table_name = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$where_sql assigned unsafely at line 454:\n $where_sql = $query_parts[&#039;where&#039;]\n$query_parts[&#039;where&#039;] used without escaping. |  |

## `frontend/templates/receipt.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 19 | 21 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 19 | 28 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table used in $wpdb-&gt;get_row($wpdb-&gt;prepare(&quot;SELECT * FROM {$table} WHERE id = %d&quot;, $donation_id))\n$table assigned unsafely at line 18:\n $table = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$donation assigned unsafely at line 19:\n $donation = $wpdb-&gt;get_row($wpdb-&gt;prepare(&quot;SELECT * FROM {$table} WHERE id = %d&quot;, $donation_id)) |  |
| 19 | 51 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table} at &quot;SELECT * FROM {$table} WHERE id = %d&quot; |  |
| 138 | 1 | ERROR | WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet | Stylesheets must be registered/enqueued via wp_enqueue_style() |  |
| 141 | 1 | ERROR | WordPress.WP.EnqueuedResources.NonEnqueuedScript | Scripts must be registered/enqueued via wp_enqueue_script() |  |
| 141 | 1 | ERROR | PluginCheck.CodeAnalysis.Offloading.OffloadedContent | Offloading images, js, css, and other scripts to your servers or any remote service is disallowed. |  |

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

## `includes/metabox.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 255 | 29 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;wpd_whatsapp_settings&#039;] |  |
| 272 | 29 | WARNING | WordPress.Security.ValidatedSanitizedInput.InputNotSanitized | Detected usage of a non-sanitized input variable: $_POST[&#039;wpd_campaign_banks&#039;] |  |

## `includes/admin/dashboard-widget.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 39 | 37 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 39 | 44 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;get_var($wpdb-&gt;prepare(&quot;SELECT SUM(amount) FROM {$table_donations} WHERE status = %s&quot;, &#039;complete&#039;))\n$table_donations assigned unsafely at line 29:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$cache_key assigned unsafely at line 32:\n $cache_key = &#039;wpd_dashboard_stats&#039;\n$stats assigned unsafely at line 33:\n $stats = wp_cache_get($cache_key, &#039;wpd_dashboard&#039;) |  |
| 42 | 34 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 42 | 41 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;get_var($wpdb-&gt;prepare(&quot;SELECT COUNT(DISTINCT email) FROM {$table_donations} WHERE status = %s&quot;, &#039;complete&#039;))\n$table_donations assigned unsafely at line 29:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$cache_key assigned unsafely at line 32:\n $cache_key = &#039;wpd_dashboard_stats&#039;\n$stats assigned unsafely at line 33:\n $stats = wp_cache_get($cache_key, &#039;wpd_dashboard&#039;) |  |
| 42 | 64 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_donations} at &quot;SELECT COUNT(DISTINCT email) FROM {$table_donations} WHERE status = %s&quot; |  |
| 48 | 28 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 48 | 35 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;get_results($wpdb-&gt;prepare(&quot;SELECT * FROM {$table_donations} WHERE status = %s ORDER BY created_at DESC LIMIT %d&quot;, &#039;complete&#039;, 5))\n$table_donations assigned unsafely at line 29:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$cache_key assigned unsafely at line 32:\n $cache_key = &#039;wpd_dashboard_stats&#039;\n$stats assigned unsafely at line 33:\n $stats = wp_cache_get($cache_key, &#039;wpd_dashboard&#039;) |  |
| 48 | 62 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_donations} at &quot;SELECT * FROM {$table_donations} WHERE status = %s ORDER BY created_at DESC LIMIT %d&quot; |  |

## `includes/db.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 110 | 10 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 110 | 10 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 110 | 14 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;get_results($wpdb-&gt;prepare(&quot;SHOW COLUMNS FROM {$table_donations} LIKE %s&quot;, &#039;subscription_id&#039;))\n$table_donations assigned unsafely at line 15:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$table_meta assigned unsafely at line 16:\n $table_meta = $wpdb-&gt;prefix . &#039;wpd_campaign_meta&#039;\n$sql_donations assigned unsafely at line 20:\n $sql_donations = &quot;CREATE TABLE {$table_donations} (\n\t\tid bigint(20) NOT NULL AUTO_INCREMENT,\n\t\tcampaign_id bigint(20) NOT NULL,\n\t\tuser_id bigint(20) NULL,\n\t\tname varchar(100) NOT NULL,\n\t\temail varchar(100) NOT NULL,\n\t\tphone varchar(20) NULL,\n\t\tamount decimal(12,2) NOT NULL,\n\t\tcurrency varchar(3) DEFAULT &#039;IDR&#039;,\n\t\tpayment_method varchar(50) NOT NULL,\n\t\tstatus enum(&#039;pending&#039;,&#039;processing&#039;,&#039;complete&#039;,&#039;failed&#039;,&#039;refunded&#039;,&#039;expired&#039;) DEFAULT &#039;pending&#039;,\n\t\tgateway varchar(50) NULL,\n\t\tgateway_txn_id varchar(100) NULL,\n\t\tmetadata longtext NULL,\n\t\tnote text NULL,\n\t\tis_anonymous tinyint(1) DEFAULT 0,\n\t\tfundraiser_id bigint(20) DEFAULT 0,\n\t\tcreated_at datetime DEFAULT CURRENT_TIMESTAMP,\n\t\tupdated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n\t\tPRIMARY KEY (id),\n\t\tKEY campaign_id (campaign_id),\n\t\tKEY user_id (user_id),\n\t\tKEY status (status),\n\t\tKEY created_at (created_at)\n\t) $charset_collate;&quot; |  |
| 111 | 10 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;query(&quot;ALTER TABLE {$table_donations} ADD COLUMN subscription_id bigint(20) DEFAULT 0 AFTER fundraiser_id&quot;)\n$table_donations assigned unsafely at line 15:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$table_meta assigned unsafely at line 16:\n $table_meta = $wpdb-&gt;prefix . &#039;wpd_campaign_meta&#039;\n$sql_donations assigned unsafely at line 20:\n $sql_donations = &quot;CREATE TABLE {$table_donations} (\n\t\tid bigint(20) NOT NULL AUTO_INCREMENT,\n\t\tcampaign_id bigint(20) NOT NULL,\n\t\tuser_id bigint(20) NULL,\n\t\tname varchar(100) NOT NULL,\n\t\temail varchar(100) NOT NULL,\n\t\tphone varchar(20) NULL,\n\t\tamount decimal(12,2) NOT NULL,\n\t\tcurrency varchar(3) DEFAULT &#039;IDR&#039;,\n\t\tpayment_method varchar(50) NOT NULL,\n\t\tstatus enum(&#039;pending&#039;,&#039;processing&#039;,&#039;complete&#039;,&#039;failed&#039;,&#039;refunded&#039;,&#039;expired&#039;) DEFAULT &#039;pending&#039;,\n\t\tgateway varchar(50) NULL,\n\t\tgateway_txn_id varchar(100) NULL,\n\t\tmetadata longtext NULL,\n\t\tnote text NULL,\n\t\tis_anonymous tinyint(1) DEFAULT 0,\n\t\tfundraiser_id bigint(20) DEFAULT 0,\n\t\tcreated_at datetime DEFAULT CURRENT_TIMESTAMP,\n\t\tupdated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n\t\tPRIMARY KEY (id),\n\t\tKEY campaign_id (campaign_id),\n\t\tKEY user_id (user_id),\n\t\tKEY status (status),\n\t\tKEY created_at (created_at)\n\t) $charset_collate;&quot; |  |
| 111 | 16 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_donations} at &quot;ALTER TABLE {$table_donations} ADD COLUMN subscription_id bigint(20) DEFAULT 0 AFTER fundraiser_id&quot; |  |
| 111 | 22 | WARNING | WordPress.DB.DirectDatabaseQuery.SchemaChange | Attempting a database schema change is discouraged. |  |

## `includes/gateways/manual.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 64 | 21 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 132 | 26 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 132 | 33 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table used in $wpdb-&gt;get_row($wpdb-&gt;prepare(&quot;SELECT * FROM {$table} WHERE id = %d&quot;, $donation_id))\n$table assigned unsafely at line 131:\n $table = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$donation assigned unsafely at line 132:\n $donation = $wpdb-&gt;get_row($wpdb-&gt;prepare(&quot;SELECT * FROM {$table} WHERE id = %d&quot;, $donation_id))\n$donation_id used without escaping. |  |
| 132 | 56 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table} at &quot;SELECT * FROM {$table} WHERE id = %d&quot; |  |

## `includes/api/campaigns-controller.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 113 | 17 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 181 | 20 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 181 | 21 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table used in $wpdb-&gt;get_results($wpdb-&gt;prepare(\n\t\t\t&quot;SELECT * FROM {$table} WHERE campaign_id = %d AND status = &#039;complete&#039; ORDER BY created_at DESC LIMIT %d OFFSET %d&quot;,\n\t\t\t$campaign_id,\n\t\t\t$per_page,\n\t\t\t$offset\n\t\t)) |  |
| 182 | 4 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table} at &quot;SELECT * FROM {$table} WHERE campaign_id = %d AND status = &#039;complete&#039; ORDER BY created_at DESC LIMIT %d OFFSET %d&quot; |  |
| 191 | 20 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 191 | 24 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table used in $wpdb-&gt;get_var($wpdb-&gt;prepare(\n\t\t&quot;SELECT COUNT(id) FROM {$table} WHERE campaign_id = %d AND status = &#039;complete&#039;&quot;,\n\t\t$campaign_id\n\t)) |  |
| 192 | 3 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table} at &quot;SELECT COUNT(id) FROM {$table} WHERE campaign_id = %d AND status = &#039;complete&#039;&quot; |  |

## `includes/functions-frontend.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 257 | 20 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 257 | 27 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table used in $wpdb-&gt;get_results($wpdb-&gt;prepare(\n &quot;SELECT * FROM {$table} WHERE campaign_id = %d AND status = &#039;complete&#039; ORDER BY created_at DESC LIMIT %d&quot;,\n $campaign_id,\n $limit\n ))\n$table assigned unsafely at line 250:\n $table = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$cache_key assigned unsafely at line 253:\n $cache_key = &#039;wpd_recent_donors_&#039; . $campaign_id . &#039;_limit_&#039; . $limit\n$campaign_id used without escaping. |  |
| 258 | 13 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table} at &quot;SELECT * FROM {$table} WHERE campaign_id = %d AND status = &#039;complete&#039; ORDER BY created_at DESC LIMIT %d&quot; |  |
| 280 | 24 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 280 | 31 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table used in $wpdb-&gt;get_var($wpdb-&gt;prepare(\n &quot;SELECT COUNT(id) FROM {$table} WHERE campaign_id = %d AND status = &#039;complete&#039;&quot;,\n $campaign_id\n ))\n$table assigned unsafely at line 274:\n $table = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$cache_key assigned unsafely at line 276:\n $cache_key = &#039;wpd_donor_count_&#039; . $campaign_id\n$campaign_id used without escaping. |  |
| 281 | 13 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table} at &quot;SELECT COUNT(id) FROM {$table} WHERE campaign_id = %d AND status = &#039;complete&#039;&quot; |  |
| 396 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 396 | 16 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 396 | 23 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_fundraisers used in $wpdb-&gt;get_results($wpdb-&gt;prepare(\n &quot;SELECT f.*, p.post_title \n FROM {$table_fundraisers} f\n JOIN {$wpdb-&gt;posts} p ON f.campaign_id = p.ID\n WHERE f.user_id = %d\n ORDER BY f.created_at DESC&quot;, $user_id\n ))\n$table_fundraisers assigned unsafely at line 393:\n $table_fundraisers = $wpdb-&gt;prefix . &#039;wpd_fundraisers&#039;\n$results assigned unsafely at line 396:\n $results = $wpdb-&gt;get_results($wpdb-&gt;prepare(\n &quot;SELECT f.*, p.post_title \n FROM {$table_fundraisers} f\n JOIN {$wpdb-&gt;posts} p ON f.campaign_id = p.ID\n WHERE f.user_id = %d\n ORDER BY f.created_at DESC&quot;, // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared\n $user_id\n ))\n$user_id assigned unsafely at line 392:\n $user_id = get_current_user_id() |  |
| 398 | 1 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_fundraisers} at FROM {$table_fundraisers} f\n |  |
| 427 | 36 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 427 | 43 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_logs used in $wpdb-&gt;get_var($wpdb-&gt;prepare(&quot;SELECT COUNT(id) FROM {$table_logs} WHERE fundraiser_id = %d&quot;, $row-&gt;id))\n$table_logs assigned unsafely at line 426:\n $table_logs = $wpdb-&gt;prefix . &#039;wpd_referral_logs&#039;\n$visit_count assigned unsafely at line 427:\n $visit_count = $wpdb-&gt;get_var($wpdb-&gt;prepare(&quot;SELECT COUNT(id) FROM {$table_logs} WHERE fundraiser_id = %d&quot;, $row-&gt;id))\n$row-&gt;id used without escaping. |  |
| 427 | 66 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_logs} at &quot;SELECT COUNT(id) FROM {$table_logs} WHERE fundraiser_id = %d&quot; |  |
| 529 | 25 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 529 | 32 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;get_row($wpdb-&gt;prepare(&quot;SELECT amount FROM {$table_donations} WHERE id = %d&quot;, $d_id))\n$table_donations assigned unsafely at line 528:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$donation_row assigned unsafely at line 529:\n $donation_row = $wpdb-&gt;get_row($wpdb-&gt;prepare(&quot;SELECT amount FROM {$table_donations} WHERE id = %d&quot;, $d_id)) |  |
| 529 | 55 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_donations} at &quot;SELECT amount FROM {$table_donations} WHERE id = %d&quot; |  |
| 548 | 29 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 548 | 36 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;get_row($wpdb-&gt;prepare(&quot;SELECT * FROM {$table_donations} WHERE id = %d&quot;, $donation_id))\n$table_donations assigned unsafely at line 547:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$table_donations assigned unsafely at line 528:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$donation assigned unsafely at line 548:\n $donation = $wpdb-&gt;get_row($wpdb-&gt;prepare(&quot;SELECT * FROM {$table_donations} WHERE id = %d&quot;, $donation_id))\n$donation_row assigned unsafely at line 529:\n $donation_row = $wpdb-&gt;get_row($wpdb-&gt;prepare(&quot;SELECT amount FROM {$table_donations} WHERE id = %d&quot;, $d_id)) |  |
| 548 | 59 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table_donations} at &quot;SELECT * FROM {$table_donations} WHERE id = %d&quot; |  |
| 582 | 29 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 582 | 29 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |

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
| 209 | 14 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 209 | 21 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table used in $wpdb-&gt;get_var($wpdb-&gt;prepare(&quot;SELECT SUM(amount) FROM {$table} WHERE campaign_id = %d AND status = &#039;complete&#039;&quot;, $campaign_id))\n$table assigned unsafely at line 206:\n $table = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$total assigned unsafely at line 209:\n $total = $wpdb-&gt;get_var($wpdb-&gt;prepare(&quot;SELECT SUM(amount) FROM {$table} WHERE campaign_id = %d AND status = &#039;complete&#039;&quot;, $campaign_id))\n$campaign_id used without escaping. |  |
| 209 | 44 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table} at &quot;SELECT SUM(amount) FROM {$table} WHERE campaign_id = %d AND status = &#039;complete&#039;&quot; |  |
| 212 | 20 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 212 | 27 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table used in $wpdb-&gt;get_var($wpdb-&gt;prepare(&quot;SELECT COUNT(DISTINCT email) FROM {$table} WHERE campaign_id = %d AND status = &#039;complete&#039;&quot;, $campaign_id))\n$table assigned unsafely at line 206:\n $table = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$total assigned unsafely at line 209:\n $total = $wpdb-&gt;get_var($wpdb-&gt;prepare(&quot;SELECT SUM(amount) FROM {$table} WHERE campaign_id = %d AND status = &#039;complete&#039;&quot;, $campaign_id))\n$campaign_id used without escaping. |  |
| 212 | 50 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table} at &quot;SELECT COUNT(DISTINCT email) FROM {$table} WHERE campaign_id = %d AND status = &#039;complete&#039;&quot; |  |
| 234 | 28 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table used in $wpdb-&gt;get_row($wpdb-&gt;prepare(&quot;SELECT * FROM {$table} WHERE id = %d&quot;, $donation_id))\n$table assigned unsafely at line 233:\n $table = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$donation assigned unsafely at line 234:\n $donation = $wpdb-&gt;get_row($wpdb-&gt;prepare(&quot;SELECT * FROM {$table} WHERE id = %d&quot;, $donation_id)) |  |
| 234 | 51 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table} at &quot;SELECT * FROM {$table} WHERE id = %d&quot; |  |

## `uninstall.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 33 | 9 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 33 | 9 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 33 | 22 | WARNING | WordPress.DB.PreparedSQL.InterpolatedNotPrepared | Use placeholders and $wpdb-&gt;prepare(); found interpolated variable {$table} at &quot;DROP TABLE IF EXISTS {$table}&quot; |  |
| 33 | 22 | WARNING | WordPress.DB.DirectDatabaseQuery.SchemaChange | Attempting a database schema change is discouraged. |  |
| 52 | 20 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 52 | 20 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |

## `frontend/templates/donor-dashboard.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 26 | 18 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 26 | 18 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |
| 124 | 26 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 124 | 26 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |

## `frontend/templates/donation-summary.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 20 | 13 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 20 | 13 | WARNING | WordPress.DB.DirectDatabaseQuery.NoCaching | Direct database call without caching detected. Consider using wp_cache_get() / wp_cache_set() or wp_cache_delete(). |  |

## `includes/api/fundraisers-controller.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 87 | 24 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |
| 106 | 24 | WARNING | WordPress.DB.DirectDatabaseQuery.DirectQuery | Use of a direct database call is discouraged. |  |

## `includes/services/email.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 39 | 24 | WARNING | PluginCheck.Security.DirectDB.UnescapedDBParameter | Unescaped parameter $table_donations used in $wpdb-&gt;get_row($wpdb-&gt;prepare(&quot;SELECT * FROM {$table_donations} WHERE id = %d&quot;, $donation_id))\n$table_donations assigned unsafely at line 34:\n $table_donations = $wpdb-&gt;prefix . &#039;wpd_donations&#039;\n$cache_key assigned unsafely at line 35:\n $cache_key = &#039;wpd_donation_&#039; . $donation_id\n$donation_id used without escaping. |  |
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

## `includes/frontend/css-loader.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 35 | 146 | WARNING | WordPress.WP.EnqueuedResourceParameters.MissingVersion | Resource version not set in call to wp_enqueue_style(). This means new versions of the style may not always be loaded due to browser caching. |  |

## `includes/admin/menu.php`

| Line | Column | Type | Code | Message | Docs |
| --- | --- | --- | --- | --- | --- |
| 121 | 93 | WARNING | WordPress.WP.EnqueuedResourceParameters.MissingVersion | Resource version not set in call to wp_enqueue_script(). This means new versions of the script may not always be loaded due to browser caching. |  |
| 122 | 108 | WARNING | WordPress.WP.EnqueuedResourceParameters.MissingVersion | Resource version not set in call to wp_enqueue_script(). This means new versions of the script may not always be loaded due to browser caching. |  |
