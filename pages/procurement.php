<div class="space-y-4">
  <div class="section-header">
    <h2>Procurement</h2>
    <button class="btn btn-primary" onclick="openPOForm()"><i data-lucide="plus" class="w-3.5 h-3.5"></i> New Purchase Order</button>
  </div>
  <div id="po-kpis" class="grid-4" style="gap:12px"></div>
  <div id="po-table"></div>

  <div id="po-modal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="card w-full max-w-md">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-bold text-[15px] text-[#111827]">New Purchase Order</h3>
        <button onclick="closePO()" class="text-slate-400"><i data-lucide="x" class="w-4 h-4"></i></button>
      </div>
      <div class="space-y-3">
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Supplier *</label><input id="po-supplier" class="bs-input" placeholder="Supplier name"/></div>
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Item Description *</label><input id="po-item" class="bs-input" placeholder="Item(s) to order"/></div>
        <div class="grid-2" style="gap:8px">
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Qty</label><input id="po-qty" type="number" class="bs-input" placeholder="1"/></div>
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Unit Price (<?= CURRENCY ?>)</label><input id="po-price" type="number" class="bs-input" placeholder="0"/></div>
        </div>
        <div class="flex gap-2 pt-2">
          <button class="btn btn-primary flex-1" onclick="savePO()">Create PO</button>
          <button class="btn btn-secondary" onclick="closePO()">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
let pos = [];
const PO_STATUS = {Pending:'#F59E0B',Approved:'#16A34A',Received:'#2563EB',Cancelled:'#EF4444'};
(async()=>{
  const rows = await fetchRows('procurement_purchase_orders');
  pos = rows.length ? rows : [
    {id:1,supplier:'Alibaba Suppliers',item:'Samsung TVs x20',qty:20,price:890000,total:17800000,status:'Approved',date:'2026-07-15'},
    {id:2,supplier:'Nairobi Wholesalers',item:'LG Fridges x10',qty:10,price:1200000,total:12000000,status:'Pending',date:'2026-07-18'},
    {id:3,supplier:'Dar Imports Ltd',item:'Cement 50kg x500',qty:500,price:15000,total:7500000,status:'Received',date:'2026-07-10'},
    {id:4,supplier:'China Direct',item:'Cooking Oil 5L x200',qty:200,price:12000,total:2400000,status:'Pending',date:'2026-07-20'},
  ];
  renderPO();
})();
function renderPO(){
  const total=pos.reduce((s,p)=>s+(p.total||p.total_amount||0),0);
  const pending=pos.filter(p=>p.status==='Pending').length;
  const approved=pos.filter(p=>p.status==='Approved').length;
  document.getElementById('po-kpis').innerHTML=
    buildKpi('Total POs',String(pos.length),'',null,'#2563EB')+
    buildKpi('Total Value',money(total),'',null,'#16A34A')+
    buildKpi('Pending Approval',String(pending),'',null,'#F59E0B')+
    buildKpi('Approved',String(approved),'',null,'#7C3AED');
  document.getElementById('po-table').innerHTML=buildTable(
    ['PO #','Supplier','Item','Qty','Total','Status','Date',''],pos,
    p=>`<tr>
      <td class="font-mono font-semibold text-[#111827]">PO-${p.id}</td>
      <td class="font-medium">${p.supplier||p.supplier_name||''}</td>
      <td class="text-slate-500">${p.item||p.description||''}</td>
      <td>${p.qty||p.quantity||0}</td>
      <td class="font-bold">${money(p.total||p.total_amount)}</td>
      <td>${buildPill(p.status,PO_STATUS[p.status]||'#94A3B8')}</td>
      <td class="text-slate-500">${p.date||p.created_at?.slice(0,10)||''}</td>
      <td>${p.status==='Pending'?`<button onclick="approvePO(${p.id})" class="text-[11px] text-green-600 font-semibold hover:underline">Approve</button>`:'—'}</td>
    </tr>`
  );lucide.createIcons();
}
function openPOForm(){document.getElementById('po-modal').classList.remove('hidden');}
function closePO(){document.getElementById('po-modal').classList.add('hidden');}
async function savePO(){
  const supplier=document.getElementById('po-supplier').value.trim();
  const item=document.getElementById('po-item').value.trim();
  if(!supplier||!item){toast('Supplier and item are required','error');return;}
  const qty=Number(document.getElementById('po-qty').value)||1;
  const price=Number(document.getElementById('po-price').value)||0;
  pos.unshift({id:uid(),supplier,item,qty,price,total:qty*price,status:'Pending',date:today()});
  try{await insertRow('procurement_purchase_orders',{supplier_name:supplier,description:item,quantity:qty,unit_price:price,total_amount:qty*price,status:'Pending'});toast('PO created!');}
  catch(e){toast('Added locally');}
  closePO();renderPO();
}
function approvePO(id){
  pos=pos.map(p=>p.id==id?{...p,status:'Approved'}:p);
  updateRow('procurement_purchase_orders',id,{status:'Approved'}).catch(()=>{});
  renderPO();toast('PO Approved');
}
</script>
