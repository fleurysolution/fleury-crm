INSERT INTO tenants (name, industry, employee_count, country, currency, timezone, status, package_id, created_at)
VALUES ('Test Complete Corp', 'Construction', 50, 'USA', 'USD', 'UTC', 'active', 2, NOW());

-- Get the last inserted ID for the user
SET @last_tenant_id = LAST_INSERT_ID();

INSERT INTO fs_users (tenant_id, first_name, last_name, email, password_hash, role_id, status, is_admin, created_at)
VALUES (@last_tenant_id, 'Test', 'Admin', 'test_complete_admin@example.com', '$2y$10$jgnh8G3qf1yXg9H5y1S1ue6H1P1P1P1P1P1P1P1P1P1P1', 1, 'active', 1, NOW());

INSERT INTO tenant_subscriptions (tenant_id, package_id, status, starts_at, ends_at, current_period_start, current_period_end)
VALUES (@last_tenant_id, 2, 'active', NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY), NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY));
