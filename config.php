<?php
// BusinessSphere ERP — Configuration
// Edit these two lines with your Supabase project details
define('SUPABASE_URL',      'https://bqrpiookucsdjvcvjrul.supabase.co');
define('SUPABASE_ANON_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImJxcnBpb29rdWNzZGp2Y3ZqcnVsIiwicm9sZSI6ImFub24iLCJpYXQiOjE3ODMyNjAxOTgsImV4cCI6MjA5ODgzNjE5OH0.qfjK9-OTsRJFuywvZFWsAFsOgMWzLIvx8Fc5-xeQuqA');
define('APP_NAME',          'BusinessSphere ERP');
define('APP_VERSION',       '2.0.0');
define('COMPANY_NAME',      'Kilimanjaro Trading Co.');
define('CURRENCY',          'TZS');

// All pages/modules available in the app
define('MODULES', json_encode([
  ['id'=>'dashboard',    'label'=>'Dashboard',       'icon'=>'layout-dashboard',  'group'=>'Core'],
  ['id'=>'crm',          'label'=>'CRM',             'icon'=>'users',             'group'=>'Core'],
  ['id'=>'sales',        'label'=>'Sales',           'icon'=>'shopping-cart',     'group'=>'Core'],
  ['id'=>'inventory',    'label'=>'Inventory',       'icon'=>'package',           'group'=>'Core'],
  ['id'=>'procurement',  'label'=>'Procurement',     'icon'=>'clipboard-check',   'group'=>'Core'],
  ['id'=>'finance',      'label'=>'Finance',         'icon'=>'wallet',            'group'=>'Core'],
  ['id'=>'reports',      'label'=>'Reports',         'icon'=>'bar-chart-3',       'group'=>'Core'],
  ['id'=>'hr',           'label'=>'HR',              'icon'=>'briefcase',         'group'=>'Core'],
  ['id'=>'manufacturing','label'=>'Manufacturing',   'icon'=>'factory',           'group'=>'Operations'],
  ['id'=>'scm',          'label'=>'Supply Chain',    'icon'=>'truck',             'group'=>'Operations'],
  ['id'=>'marketing',    'label'=>'Marketing',       'icon'=>'megaphone',         'group'=>'Operations'],
  ['id'=>'ecommerce',    'label'=>'E-Commerce',      'icon'=>'store',             'group'=>'Operations'],
  ['id'=>'pos',          'label'=>'Point of Sale',   'icon'=>'shopping-bag',      'group'=>'Operations'],
  ['id'=>'documents',    'label'=>'Documents',       'icon'=>'file-text',         'group'=>'Operations'],
  ['id'=>'projects',     'label'=>'Projects',        'icon'=>'kanban',            'group'=>'Operations'],
  ['id'=>'support',      'label'=>'Support',         'icon'=>'headphones',        'group'=>'Operations'],
  ['id'=>'analytics',    'label'=>'Analytics',       'icon'=>'gauge',             'group'=>'Intelligence'],
  ['id'=>'notifications','label'=>'Notifications',   'icon'=>'bell',              'group'=>'Intelligence'],
  ['id'=>'workflows',    'label'=>'Workflows',       'icon'=>'git-branch',        'group'=>'Intelligence'],
  ['id'=>'ai',           'label'=>'AI Assistant',    'icon'=>'brain',             'group'=>'Intelligence'],
  ['id'=>'settings',     'label'=>'Settings',        'icon'=>'settings',          'group'=>'Intelligence'],
  ['id'=>'microfinance', 'label'=>'Microfinance',    'icon'=>'hand-coins',        'group'=>'Industry'],
  ['id'=>'healthcare',   'label'=>'Healthcare',      'icon'=>'heart-pulse',       'group'=>'Industry'],
  ['id'=>'school',       'label'=>'School Mgmt',     'icon'=>'school',            'group'=>'Industry'],
  ['id'=>'pharmacy',     'label'=>'Pharmacy',        'icon'=>'pill',              'group'=>'Industry'],
  ['id'=>'hotel',        'label'=>'Hotel Mgmt',      'icon'=>'hotel',             'group'=>'Industry'],
  ['id'=>'fleet',        'label'=>'Fleet Mgmt',      'icon'=>'car',               'group'=>'Industry'],
  ['id'=>'banking',      'label'=>'Banking / MFI',   'icon'=>'credit-card',       'group'=>'Industry'],
  ['id'=>'restaurant',   'label'=>'Restaurant',      'icon'=>'utensils-crossed',  'group'=>'Industry'],
  ['id'=>'vicoba',       'label'=>'VICOBA/SACCOS',   'icon'=>'users-2',           'group'=>'Industry'],
  ['id'=>'community',    'label'=>'Community Groups','icon'=>'tree-pine',         'group'=>'Industry'],
  ['id'=>'pos_system',   'label'=>'POS System',      'icon'=>'scan-line',         'group'=>'Industry'],
  ['id'=>'employee',     'label'=>'Employee Portal', 'icon'=>'user',              'group'=>'Other'],
  ['id'=>'integrations', 'label'=>'Integrations',    'icon'=>'globe',             'group'=>'Other'],
]));
