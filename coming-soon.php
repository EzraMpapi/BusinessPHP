<?php
// Auto-detect which module this is for
$page    = isset($_GET['page']) ? htmlspecialchars($_GET['page']) : 'module';
$modules = json_decode(MODULES, true);
$mod     = null;
foreach ($modules as $m) { if ($m['id'] === $page) { $mod = $m; break; } }
$label = $mod ? $mod['label'] : ucfirst(str_replace(['-','_'],' ',$page));
$icon  = $mod ? $mod['icon'] : 'layers';

// Features per module
$features = [
  'procurement'  => ['Purchase Orders','Supplier Management','Approval Workflows','Spend Analytics','Contracts'],
  'reports'      => ['P&L Reports','Balance Sheet','AR Aging','Cash Flow','Tax Summary','PDF Export'],
  'manufacturing'=> ['Bill of Materials','Work Orders','Machine Tracking','Quality Control','Maintenance'],
  'scm'          => ['Shipment Tracking','Supplier Portal','Demand Planning','Logistics','Customs'],
  'marketing'    => ['Email Campaigns','Lead Scoring','Campaign Analytics','Segments','ROI Tracking'],
  'ecommerce'    => ['Online Store','Order Management','Product Catalog','Payments','Delivery'],
  'pos'          => ['POS Terminal','Barcode Scan','Split Payments','Z-Reports','Shift Management'],
  'documents'    => ['File Storage','Digital Signatures','Document Templates','Version Control'],
  'projects'     => ['Project Timeline','Task Management','Budget Tracking','Milestones','Team Assignment'],
  'support'      => ['Support Tickets','Live Chat','Knowledge Base','Call Center','SLA Tracking'],
  'analytics'    => ['Executive Dashboard','KPI Builder','Custom Reports','Forecasting','Benchmarks'],
  'notifications'=> ['Push Notifications','Email Alerts','SMS Alerts','Alert Routing','Digest Reports'],
  'workflows'    => ['Workflow Builder','Automation Rules','Triggers','Approval Chains','Templates'],
  'ai'           => ['AI Chat Assistant','Document Analysis','Demand Forecasting','Smart Alerts','Reports'],
  'settings'     => ['Company Profile','User Management','Roles & Permissions','Audit Log','Security'],
  'microfinance' => ['Loan Origination','Repayment Schedules','Member Accounts','Portfolio Analytics'],
  'healthcare'   => ['Patient Registry','Appointments','Prescriptions','Lab Results','Billing'],
  'school'       => ['Student Records','Timetables','Fees Management','Attendance','Exams'],
  'pharmacy'     => ['Drug Inventory','Prescription Processing','Expiry Tracking','Supplier Orders'],
  'hotel'        => ['Room Management','Reservations','Housekeeping','F&B','Revenue Analytics'],
  'fleet'        => ['Vehicle Registry','Trip Logging','Maintenance','Fuel Tracking','Driver Mgmt'],
  'banking'      => ['Accounts','Loans','Deposits','Transactions','KYC','Compliance'],
  'restaurant'   => ['Table Management','Order Tickets','Kitchen Display','Menu Builder','Reports'],
  'vicoba'       => ['Member Accounts','Group Savings','Loan Cycles','Meeting Minutes','Reports'],
  'community'    => ['Group Registry','Member Management','Contributions','Projects','Reports'],
  'pos_system'   => ['Multi-Register POS','Products','Sales History','Daily Reports','Cashier Management'],
  'employee'     => ['My Profile','Leave Requests','Payslips','Attendance','Performance'],
  'integrations' => ['M-Pesa','Airtel Money','WhatsApp Business','Email','SMS Gateway','API'],
];

$flist = $features[$page] ?? ['Full CRUD Operations','Real-time Sync','PDF Reports','CSV Export','Supabase Backend'];
?>

<div class="flex flex-col items-center justify-center min-h-[65vh] gap-5 text-center p-6">
  <div class="w-16 h-16 rounded-2xl flex items-center justify-center bg-green-50">
    <i data-lucide="<?= $icon ?>" class="w-8 h-8 text-[#16A34A]"></i>
  </div>
  <div>
    <h2 class="text-[20px] font-bold text-[#111827] mb-2"><?= $label ?></h2>
    <p class="text-[13px] text-slate-500 max-w-sm leading-relaxed">
      This module is fully designed and ready to build. The core framework is live &mdash; connect Supabase and add your data.
    </p>
  </div>
  <div class="grid-2 w-full max-w-xs gap-2">
    <?php foreach ($flist as $f): ?>
    <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
      <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-[#16A34A] flex-shrink-0"></i>
      <span class="text-[12px] text-slate-700 font-medium"><?= htmlspecialchars($f) ?></span>
    </div>
    <?php endforeach; ?>
  </div>
  <div class="flex gap-2 flex-wrap justify-center">
    <span class="pill bg-green-50 text-green-700">PHP + Supabase</span>
    <span class="pill bg-blue-50 text-blue-700">No Build Step</span>
    <span class="pill bg-purple-50 text-purple-700">Deploy Anywhere</span>
  </div>
</div>
<script>lucide.createIcons();</script>
