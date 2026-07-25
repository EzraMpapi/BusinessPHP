<div class="space-y-4">
  <div class="section-header"><h2>Microfinance Management</h2>
    <button class="btn btn-primary" onclick="openLoanForm()"><i data-lucide="plus" class="w-3.5 h-3.5"></i> New Loan</button></div>
  <div id="mfi-kpis" class="grid-4" style="gap:12px">
    <div class="kpi"><div class="spinner"></div></div><div class="kpi"><div class="spinner"></div></div>
    <div class="kpi"><div class="spinner"></div></div><div class="kpi"><div class="spinner"></div></div></div>
  <div id="loan-table"></div>
  <div id="loan-modal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="card w-full max-w-md"><div class="flex items-center justify-between mb-4"><h3 class="font-bold text-[15px]">New Loan Application</h3>
      <button onclick="document.getElementById('loan-modal').classList.add('hidden')" class="text-slate-400"><i data-lucide="x" class="w-4 h-4"></i></button></div>
      <div class="space-y-3">
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Borrower Name *</label><input id="l-name" class="bs-input" placeholder="Full name"/></div>
        <div class="grid-2" style="gap:8px">
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Amount (<?= CURRENCY ?>)</label><input id="l-amount" type="number" class="bs-input" placeholder="0"/></div>
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Term (months)</label><input id="l-term" type="number" class="bs-input" placeholder="12"/></div>
        </div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Purpose</label>
          <select id="l-purpose" class="bs-input"><option>Business Capital</option><option>Agriculture</option><option>Education</option><option>Housing</option><option>Emergency</option></select></div>
        <div class="flex gap-2 pt-2">
          <button class="btn btn-primary flex-1" onclick="saveLoan()">Submit Application</button>
          <button class="btn btn-secondary" onclick="document.getElementById('loan-modal').classList.add('hidden')">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
let loans=[
  {id:'LN-1021',borrower:'Mama Ntilie Msaidizi',amount:500000,balance:320000,term:12,rate:18,status:'Active',purpose:'Business Capital',next_due:'2026-08-01'},
  {id:'LN-1020',borrower:'Juma Kilimo Farms',amount:1200000,balance:0,term:24,rate:15,status:'Paid',purpose:'Agriculture',next_due:'—'},
  {id:'LN-1019',borrower:'Fatuma Biashara',amount:800000,balance:650000,term:12,rate:18,status:'Active',purpose:'Business Capital',next_due:'2026-07-28'},
  {id:'LN-1018',borrower:'Hassan Ujenzi',amount:2000000,balance:2000000,term:36,rate:12,status:'Pending',purpose:'Housing',next_due:'—'},
  {id:'LN-1017',borrower:'Amina Elimu Fund',amount:400000,balance:100000,term:6,rate:18,status:'Active',purpose:'Education',next_due:'2026-07-30'},
];
const LS={Active:'#16A34A',Paid:'#2563EB',Pending:'#F59E0B',Overdue:'#EF4444'};
function render(){
  const total=loans.reduce((s,l)=>s+(l.amount||0),0);
  const outstanding=loans.reduce((s,l)=>s+(l.balance||0),0);
  const active=loans.filter(l=>l.status==='Active').length;
  const pending=loans.filter(l=>l.status==='Pending').length;
  document.getElementById('mfi-kpis').innerHTML=
    buildKpi('Portfolio Value',money(total),'',null,'#2563EB')+
    buildKpi('Outstanding',money(outstanding),'',null,'#EF4444')+
    buildKpi('Active Loans',String(active),'',null,'#16A34A')+
    buildKpi('Pending Approval',String(pending),'',null,'#F59E0B');
  document.getElementById('loan-table').innerHTML=buildTable(
    ['Loan #','Borrower','Amount','Balance','Rate','Term','Purpose','Status','Next Due',''],
    loans,l=>`<tr>
      <td class="font-mono font-semibold text-[#111827]">${l.id}</td>
      <td class="font-medium">${l.borrower}</td>
      <td class="font-bold">${money(l.amount)}</td>
      <td class="${l.balance>0?'text-red-600 font-bold':'text-green-600'}">${money(l.balance)}</td>
      <td>${l.rate}%</td>
      <td>${l.term}mo</td>
      <td class="text-slate-500">${l.purpose}</td>
      <td>${buildPill(l.status,LS[l.status]||'#94A3B8')}</td>
      <td class="text-slate-500">${l.next_due}</td>
      <td>${l.status==='Pending'?`<button onclick="approveLoan('${l.id}')" class="text-[11px] text-green-600 font-semibold">Approve</button>`:''}</td>
    </tr>`);
  lucide.createIcons();
}
render();
function openLoanForm(){document.getElementById('loan-modal').classList.remove('hidden');}
async function saveLoan(){
  const name=document.getElementById('l-name').value.trim();
  if(!name){toast('Name required','error');return;}
  const amount=Number(document.getElementById('l-amount').value)||0;
  loans.unshift({id:'LN-'+uid(),borrower:name,amount,balance:amount,
    term:Number(document.getElementById('l-term').value)||12,rate:18,
    status:'Pending',purpose:document.getElementById('l-purpose').value,next_due:'—'});
  try{await insertRow('microfinance_loans',{borrower_name:name,loan_amount:amount,status:'Pending'});toast('Application submitted!');}catch(e){toast('Added locally');}
  document.getElementById('loan-modal').classList.add('hidden');render();
}
function approveLoan(id){loans=loans.map(l=>l.id===id?{...l,status:'Active',next_due:today()}:l);updateRow('microfinance_loans',id,{status:'Active'}).catch(()=>{});render();toast('Loan approved!');}
</script>