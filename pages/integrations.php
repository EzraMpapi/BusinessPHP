<div class="space-y-4">
  <div class="section-header"><h2>Integration Hub</h2>
    <button class="btn btn-primary" onclick="toast('Contact support to add new integrations')"><i data-lucide="plus" class="w-3.5 h-3.5"></i> Request Integration</button></div>
  <div class="grid-3" style="gap:12px">
    <div id="int-kpis"></div><div id="int-kpis2"></div><div id="int-kpis3"></div>
  </div>
  <div id="integrations-grid" class="grid-2" style="gap:12px"></div>
</div>
<script>
const integrations=[
  {id:1,name:'M-Pesa',provider:'Vodacom',category:'Mobile Money',status:'Connected',desc:'Accept M-Pesa payments and send disbursements',icon:'smartphone',color:'#16A34A',txns:1247},
  {id:2,name:'Airtel Money',provider:'Airtel Tanzania',category:'Mobile Money',status:'Connected',desc:'Accept Airtel Money payments',icon:'smartphone',color:'#EF4444',txns:432},
  {id:3,name:'WhatsApp Business',provider:'Meta',category:'Communication',status:'Connected',desc:'Send invoices, receipts and alerts via WhatsApp',icon:'message-square',color:'#16A34A',txns:89},
  {id:4,name:'Email (SMTP)',provider:'Gmail / SendGrid',category:'Communication',status:'Connected',desc:'Send automated emails and reports',icon:'mail',color:'#2563EB',txns:2341},
  {id:5,name:'TRA e-Filing',provider:'Tanzania Revenue Authority',category:'Government',status:'Available',desc:'Submit tax returns directly to TRA',icon:'file-check',color:'#7C3AED',txns:0},
  {id:6,name:'GEPG',provider:'eGA Tanzania',category:'Government',status:'Available',desc:'Government e-payment gateway',icon:'landmark',color:'#0891B2',txns:0},
  {id:7,name:'Zantel / TIGO Pesa',provider:'Zantel',category:'Mobile Money',status:'Available',desc:'Accept TIGO Pesa payments',icon:'smartphone',color:'#F59E0B',txns:0},
  {id:8,name:'Pesalink',provider:'Tanzania Bankers Association',category:'Banking',status:'Available',desc:'Bank-to-bank transfers via Pesalink',icon:'arrow-right-left',color:'#2563EB',txns:0},
  {id:9,name:'SMS Gateway',provider:'AfricasTalking',category:'Communication',status:'Connected',desc:'Bulk SMS alerts and notifications',icon:'message-circle',color:'#64748B',txns:521},
  {id:10,name:'Google Workspace',provider:'Google',category:'Productivity',status:'Available',desc:'Sync with Google Calendar and Drive',icon:'globe',color:'#4285F4',txns:0},
  {id:11,name:'QuickBooks',provider:'Intuit',category:'Accounting',status:'Available',desc:'Sync transactions with QuickBooks',icon:'bar-chart-3',color:'#2CA01C',txns:0},
  {id:12,name:'Supabase',provider:'Supabase Inc.',category:'Database',status:'Connected',desc:'Your primary database — fully connected',icon:'database',color:'#3ECF8E',txns:0},
];
const connected=integrations.filter(i=>i.status==='Connected').length;
const available=integrations.filter(i=>i.status==='Available').length;
document.getElementById('int-kpis').innerHTML=buildKpi('Connected',String(connected),'',null,'#16A34A');
document.getElementById('int-kpis2').innerHTML=buildKpi('Available',String(available),'',null,'#2563EB');
document.getElementById('int-kpis3').innerHTML=buildKpi('Total',String(integrations.length),'',null,'#7C3AED');
document.getElementById('integrations-grid').innerHTML=integrations.map(int=>`
  <div class="card">
    <div class="flex items-start gap-3">
      <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:${int.color}18">
        <i data-lucide="${int.icon}" class="w-5 h-5" style="color:${int.color}"></i></div>
      <div class="flex-1 min-w-0">
        <div class="flex items-center justify-between gap-2">
          <h3 class="font-bold text-[13px] text-[#111827]">${int.name}</h3>
          ${buildPill(int.status,int.status==='Connected'?'#16A34A':'#94A3B8')}
        </div>
        <p class="text-[10.5px] text-slate-400 mt-0.5">${int.provider} &middot; ${int.category}</p>
        <p class="text-[12px] text-slate-500 mt-1">${int.desc}</p>
        ${int.txns?`<p class="text-[11px] text-[#16A34A] mt-1 font-semibold">${int.txns.toLocaleString()} transactions</p>`:''
}
        <div class="flex gap-2 mt-3">
          ${int.status==='Connected'
            ?'<button class="btn btn-secondary" style="padding:4px 10px;font-size:11px" onclick="toast(\'Settings for \'+this.dataset.name)" data-name="'+int.name+'">Settings</button><button class="btn btn-danger" style="padding:4px 10px;font-size:11px" onclick="if(confirm(\'Disconnect?\'))toast(\'Disconnected: \'+\''+int.name+'\')">Disconnect</button>'
            :'<button class="btn btn-primary" style="padding:4px 10px;font-size:11px" onclick="toast(\'Request sent to connect: \'+\''+int.name+'\')">Connect</button>'
          }
        </div>
      </div>
    </div>
  </div>`).join('');
lucide.createIcons();
</script>