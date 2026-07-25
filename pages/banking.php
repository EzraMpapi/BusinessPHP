<div class="space-y-4">
  <div class="section-header"><h2>Banking &amp; MFI</h2>
    <button class="btn btn-primary" onclick="openAccountForm()"><i data-lucide="plus" class="w-3.5 h-3.5"></i> Open Account</button></div>
  <div id="bnk-kpis" class="grid-4" style="gap:12px">
    <div class="kpi"><div class="spinner"></div></div><div class="kpi"><div class="spinner"></div></div>
    <div class="kpi"><div class="spinner"></div></div><div class="kpi"><div class="spinner"></div></div></div>
  <div id="accounts-table"></div>
  <div id="acc-modal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="card w-full max-w-md"><div class="flex items-center justify-between mb-4"><h3 class="font-bold text-[15px]">Open Account</h3>
      <button onclick="document.getElementById('acc-modal').classList.add('hidden')" class="text-slate-400"><i data-lucide="x" class="w-4 h-4"></i></button></div>
      <div class="space-y-3">
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Account Holder *</label><input id="a-name" class="bs-input" placeholder="Full name"/></div>
        <div class="grid-2" style="gap:8px">
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Account Type</label>
            <select id="a-type" class="bs-input"><option>Savings</option><option>Current</option><option>Fixed Deposit</option></select></div>
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Opening Balance</label><input id="a-balance" type="number" class="bs-input" placeholder="0"/></div>
        </div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Phone</label><input id="a-phone" class="bs-input" placeholder="+255 xxx xxx xxx"/></div>
        <div class="flex gap-2 pt-2">
          <button class="btn btn-primary flex-1" onclick="saveAccount()">Open Account</button>
          <button class="btn btn-secondary" onclick="document.getElementById('acc-modal').classList.add('hidden')">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
let accounts=[
  {id:'ACC-10021',holder:'Mama Asha Savings Group',type:'Savings',balance:4500000,interest:3.5,transactions:24,status:'Active',opened:'2024-01-15'},
  {id:'ACC-10020',holder:'Juma Hardware Ltd',type:'Current',balance:12800000,interest:0,transactions:156,status:'Active',opened:'2023-06-01'},
  {id:'ACC-10019',holder:'Kilosa Women Group',type:'Savings',balance:890000,interest:4,transactions:12,status:'Active',opened:'2025-03-10'},
  {id:'ACC-10018',holder:'Moshi Farmers Coop',type:'Fixed Deposit',balance:5000000,interest:8,transactions:2,status:'Active',opened:'2026-01-01'},
  {id:'ACC-10017',holder:'Hassan Mohamed',type:'Savings',balance:320000,interest:3.5,transactions:45,status:'Active',opened:'2022-09-15'},
];
const ATS={Savings:'#16A34A',Current:'#2563EB','Fixed Deposit':'#7C3AED'};
function render(){
  const totalDeposits=accounts.reduce((s,a)=>s+(a.balance||0),0);
  const active=accounts.filter(a=>a.status==='Active').length;
  const totalTx=accounts.reduce((s,a)=>s+(a.transactions||0),0);
  document.getElementById('bnk-kpis').innerHTML=
    buildKpi('Total Accounts',String(accounts.length),'',null,'#2563EB')+
    buildKpi('Total Deposits',money(totalDeposits),'+8%',true,'#16A34A')+
    buildKpi('Active Accounts',String(active),'',null,'#7C3AED')+
    buildKpi('Total Transactions',String(totalTx),'',null,'#F59E0B');
  document.getElementById('accounts-table').innerHTML=buildTable(
    ['Account #','Holder','Type','Balance','Interest','Transactions','Status','Opened',''],
    accounts,a=>`<tr>
      <td class="font-mono font-semibold text-[#111827]">${a.id}</td>
      <td class="font-semibold">${a.holder}</td>
      <td>${buildPill(a.type,ATS[a.type]||'#94A3B8')}</td>
      <td class="font-bold">${money(a.balance)}</td>
      <td>${a.interest}% p.a.</td>
      <td>${a.transactions}</td>
      <td>${buildPill(a.status,'#16A34A')}</td>
      <td class="text-slate-500 text-[11px]">${a.opened}</td>
      <td><button onclick="doDeposit('${a.id}')" class="text-[11px] text-green-600 font-semibold mr-2">Deposit</button>
          <button onclick="doWithdraw('${a.id}')" class="text-[11px] text-red-500 font-semibold">Withdraw</button></td>
    </tr>`);
  lucide.createIcons();
}
render();
function openAccountForm(){document.getElementById('acc-modal').classList.remove('hidden');}
async function saveAccount(){
  const holder=document.getElementById('a-name').value.trim();
  if(!holder){toast('Name required','error');return;}
  const balance=Number(document.getElementById('a-balance').value)||0;
  const type=document.getElementById('a-type').value;
  accounts.unshift({id:'ACC-'+uid(),holder,type,balance,interest:type==='Fixed Deposit'?8:type==='Savings'?3.5:0,transactions:1,status:'Active',opened:today()});
  try{await insertRow('bank_accounts',{account_holder:holder,account_type:type,balance,status:'Active'});toast('Account opened!');}catch(e){toast('Added locally');}
  document.getElementById('acc-modal').classList.add('hidden');render();
}
function doDeposit(id){const a=Number(prompt('Deposit amount:'));if(!a||isNaN(a))return;accounts=accounts.map(ac=>ac.id==id?{...ac,balance:ac.balance+a,transactions:ac.transactions+1}:ac);render();toast('Deposited '+money(a));}
function doWithdraw(id){const a=Number(prompt('Withdraw amount:'));if(!a||isNaN(a))return;const acc=accounts.find(ac=>ac.id==id);if(a>acc.balance){toast('Insufficient balance','error');return;}accounts=accounts.map(ac=>ac.id==id?{...ac,balance:ac.balance-a,transactions:ac.transactions+1}:ac);render();toast('Withdrawn '+money(a));}
</script>