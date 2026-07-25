<div class="space-y-4">
  <div class="section-header"><h2>Restaurant Management</h2>
    <button class="btn btn-primary" onclick="openOrderForm()"><i data-lucide="plus" class="w-3.5 h-3.5"></i> New Order</button></div>
  <div id="rst-kpis" class="grid-4" style="gap:12px">
    <div class="kpi"><div class="spinner"></div></div><div class="kpi"><div class="spinner"></div></div>
    <div class="kpi"><div class="spinner"></div></div><div class="kpi"><div class="spinner"></div></div></div>
  <div class="grid-2" style="gap:16px">
    <div>
      <h3 class="text-[14px] font-bold text-[#111827] mb-3">Table Status</h3>
      <div id="tables-grid" class="grid-4" style="gap:8px"></div>
    </div>
    <div><h3 class="text-[14px] font-bold text-[#111827] mb-3">Active Orders</h3><div id="orders-list"></div></div>
  </div>
  <div id="ord-modal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="card w-full max-w-md"><div class="flex items-center justify-between mb-4"><h3 class="font-bold text-[15px]">New Order</h3>
      <button onclick="document.getElementById('ord-modal').classList.add('hidden')" class="text-slate-400"><i data-lucide="x" class="w-4 h-4"></i></button></div>
      <div class="space-y-3">
        <div class="grid-2" style="gap:8px">
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Table #</label>
            <select id="o-table" class="bs-input"><?php for($i=1;$i<=12;$i++) echo "<option>$i</option>"; ?></select></div>
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Guests</label><input id="o-guests" type="number" class="bs-input" value="2" min="1"/></div>
        </div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Items Ordered</label><input id="o-items" class="bs-input" placeholder="e.g. Ugali+Nyama, Juice x2"/></div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Total Amount (<?= CURRENCY ?>)</label><input id="o-amount" type="number" class="bs-input" placeholder="0"/></div>
        <div class="flex gap-2 pt-2">
          <button class="btn btn-primary flex-1" onclick="saveOrder()">Place Order</button>
          <button class="btn btn-secondary" onclick="document.getElementById('ord-modal').classList.add('hidden')">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
let tables=Array.from({length:12},(_,i)=>({number:i+1,status:['Available','Occupied','Available','Reserved'][i%4],guests:i%4===1?[2,3,4,2][i%4]:0}));
let orders=[
  {id:'ORD-091',table:2,guests:3,items:'Ugali + Nyama x3, Juice x3',amount:45000,status:'Cooking',time:'12:15'},
  {id:'ORD-090',table:5,guests:2,items:'Pilau x2, Coconut Water x2',amount:28000,status:'Ready',time:'12:10'},
  {id:'ORD-089',table:8,guests:4,items:'Biryani x4, Soda x4',amount:72000,status:'Served',time:'12:00'},
];
const TS={Available:'#16A34A',Occupied:'#EF4444',Reserved:'#F59E0B',Cleaning:'#94A3B8'};
const OS={Pending:'#F59E0B',Cooking:'#2563EB',Ready:'#16A34A',Served:'#94A3B8'};
function render(){
  const rev=orders.filter(o=>o.status==='Served').reduce((s,o)=>s+(o.amount||0),0);
  const active=orders.filter(o=>o.status!=='Served').length;
  const occupied=tables.filter(t=>t.status==='Occupied').length;
  document.getElementById('rst-kpis').innerHTML=
    buildKpi('Tables',String(tables.length)+' total','',null,'#2563EB')+
    buildKpi('Occupied',String(occupied),'',null,'#EF4444')+
    buildKpi('Active Orders',String(active),'',null,'#F59E0B')+
    buildKpi('Revenue Today',money(rev),'',null,'#16A34A');
  document.getElementById('tables-grid').innerHTML=tables.map(t=>`
    <div class="card p-2 text-center cursor-pointer" style="border-color:${TS[t.status]}40">
      <p class="text-[13px] font-bold text-[#111827]">T${t.number}</p>
      <p class="text-[10px]" style="color:${TS[t.status]}">${t.status}</p>
      ${t.guests?`<p class="text-[9px] text-slate-400">${t.guests} guests</p>`:''}
    </div>`).join('');
  document.getElementById('orders-list').innerHTML=orders.map(o=>`
    <div class="card mb-2 p-3">
      <div class="flex items-center justify-between mb-1">
        <span class="font-bold text-[13px]">Table ${o.table} &mdash; ${o.id}</span>
        ${buildPill(o.status,OS[o.status]||'#94A3B8')}
      </div>
      <p class="text-[12px] text-slate-500">${o.items}</p>
      <div class="flex items-center justify-between mt-2">
        <span class="font-bold text-[#16A34A]">${money(o.amount)}</span>
        ${o.status!=='Served'?`<button onclick="advOrder('${o.id}')" class="btn btn-primary" style="padding:4px 10px;font-size:11px">Next Stage</button>`:''}
      </div>
    </div>`).join('');
  lucide.createIcons();
}
render();
const NEXT_STATUS={Pending:'Cooking',Cooking:'Ready',Ready:'Served'};
function advOrder(id){orders=orders.map(o=>o.id===id?{...o,status:NEXT_STATUS[o.status]||o.status}:o);render();}
function openOrderForm(){document.getElementById('ord-modal').classList.remove('hidden');}
async function saveOrder(){
  const table=Number(document.getElementById('o-table').value);
  const amount=Number(document.getElementById('o-amount').value)||0;
  if(!amount){toast('Amount required','error');return;}
  tables=tables.map(t=>t.number===table?{...t,status:'Occupied',guests:Number(document.getElementById('o-guests').value)||1}:t);
  orders.unshift({id:'ORD-'+uid(),table,guests:Number(document.getElementById('o-guests').value)||1,
    items:document.getElementById('o-items').value,amount,status:'Pending',time:new Date().toTimeString().slice(0,5)});
  try{await insertRow('restaurant_orders',{table_number:table,total_amount:amount,status:'Pending'});toast('Order placed!');}catch(e){toast('Added locally');}
  document.getElementById('ord-modal').classList.add('hidden');render();
}
</script>