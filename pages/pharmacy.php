<div class="space-y-4">
  <div class="section-header"><h2>Pharmacy Management</h2>
    <button class="btn btn-primary" onclick="openDrugForm()"><i data-lucide="plus" class="w-3.5 h-3.5"></i> Add Drug</button></div>
  <div id="phm-kpis" class="grid-4" style="gap:12px">
    <div class="kpi"><div class="spinner"></div></div><div class="kpi"><div class="spinner"></div></div>
    <div class="kpi"><div class="spinner"></div></div><div class="kpi"><div class="spinner"></div></div></div>
  <div id="drug-table"></div>
  <div id="drug-modal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4">
    <div class="card w-full max-w-md"><div class="flex items-center justify-between mb-4"><h3 class="font-bold text-[15px]">Add Drug</h3>
      <button onclick="document.getElementById('drug-modal').classList.add('hidden')" class="text-slate-400"><i data-lucide="x" class="w-4 h-4"></i></button></div>
      <div class="space-y-3">
        <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Drug Name *</label><input id="d-name" class="bs-input" placeholder="Generic name"/></div>
        <div class="grid-2" style="gap:8px">
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Qty (units)</label><input id="d-qty" type="number" class="bs-input" placeholder="0"/></div>
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Price (<?= CURRENCY ?>)</label><input id="d-price" type="number" class="bs-input" placeholder="0"/></div>
        </div>
        <div class="grid-2" style="gap:8px">
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Expiry Date</label><input id="d-expiry" type="date" class="bs-input"/></div>
          <div><label class="text-[11.5px] font-semibold text-slate-600 block mb-1">Category</label>
            <select id="d-cat" class="bs-input"><option>Antibiotics</option><option>Analgesics</option><option>Antivirals</option><option>Vitamins</option><option>OTC</option></select></div>
        </div>
        <div class="flex gap-2 pt-2">
          <button class="btn btn-primary flex-1" onclick="saveDrug()">Add Drug</button>
          <button class="btn btn-secondary" onclick="document.getElementById('drug-modal').classList.add('hidden')">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
let drugs=[
  {id:1,name:'Amoxicillin 500mg',category:'Antibiotics',qty:500,reorder:100,price:1500,expiry:'2027-06-30',status:'In Stock'},
  {id:2,name:'Paracetamol 500mg',category:'Analgesics',qty:1200,reorder:200,price:500,expiry:'2026-12-31',status:'In Stock'},
  {id:3,name:'Metronidazole 400mg',category:'Antibiotics',qty:45,reorder:100,price:2000,expiry:'2026-09-30',status:'Low Stock'},
  {id:4,name:'Vitamin C 1000mg',category:'Vitamins',qty:300,reorder:50,price:800,expiry:'2027-03-31',status:'In Stock'},
  {id:5,name:'ORS Sachet',category:'OTC',qty:800,reorder:200,price:300,expiry:'2026-08-15',status:'In Stock'},
  {id:6,name:'Artemether 20mg',category:'Antivirals',qty:12,reorder:50,price:4500,expiry:'2026-10-31',status:'Low Stock'},
];
const DS={'In Stock':'#16A34A','Low Stock':'#F59E0B','Out of Stock':'#EF4444','Expired':'#7C3AED'};
function render(){
  const totalDrugs=drugs.length;
  const lowStock=drugs.filter(d=>d.status==='Low Stock'||d.qty<=d.reorder).length;
  const totalValue=drugs.reduce((s,d)=>s+(d.qty||0)*(d.price||0),0);
  const expiringSoon=drugs.filter(d=>d.expiry&&new Date(d.expiry)<new Date(Date.now()+90*86400000)).length;
  document.getElementById('phm-kpis').innerHTML=
    buildKpi('Total Drugs',String(totalDrugs),'',null,'#2563EB')+
    buildKpi('Stock Value',money(totalValue),'',null,'#16A34A')+
    buildKpi('Low Stock',String(lowStock),'',null,'#F59E0B')+
    buildKpi('Expiring Soon',String(expiringSoon),'',null,'#EF4444');
  document.getElementById('drug-table').innerHTML=buildTable(
    ['Drug Name','Category','Qty','Reorder','Unit Price','Expiry','Status',''],
    drugs,d=>`<tr class="${d.status==='Low Stock'?'bg-yellow-50':''}">
      <td class="font-semibold text-[#111827]">${d.name}</td>
      <td><span class="pill bg-purple-50 text-purple-700">${d.category}</span></td>
      <td class="font-bold ${d.qty<=d.reorder?'text-red-500':''}">${d.qty}</td>
      <td class="text-slate-500">${d.reorder}</td>
      <td>${money(d.price)}</td>
      <td class="text-slate-500">${d.expiry}</td>
      <td>${buildPill(d.status,DS[d.status]||'#94A3B8')}</td>
      <td><button onclick="dispenseDrug(${d.id})" class="text-[11px] text-blue-600 font-semibold">Dispense</button></td>
    </tr>`);
  lucide.createIcons();
}
render();
function openDrugForm(){document.getElementById('drug-modal').classList.remove('hidden');}
async function saveDrug(){
  const name=document.getElementById('d-name').value.trim();
  if(!name){toast('Name required','error');return;}
  const qty=Number(document.getElementById('d-qty').value)||0;
  const reorder=Math.round(qty*0.2);
  drugs.unshift({id:uid(),name,category:document.getElementById('d-cat').value,
    qty,reorder,price:Number(document.getElementById('d-price').value)||0,
    expiry:document.getElementById('d-expiry').value,status:qty>reorder?'In Stock':'Low Stock'});
  try{await insertRow('pharmacy_drugs',{drug_name:name,quantity:qty,status:'In Stock'});toast('Drug added!');}catch(e){toast('Added locally');}
  document.getElementById('drug-modal').classList.add('hidden');render();
}
function dispenseDrug(id){
  const qty=Number(prompt('Quantity to dispense:'));
  if(!qty||isNaN(qty))return;
  drugs=drugs.map(d=>{
    if(d.id!=id)return d;
    const newQty=Math.max(0,d.qty-qty);
    return{...d,qty:newQty,status:newQty===0?'Out of Stock':newQty<=d.reorder?'Low Stock':'In Stock'};
  });
  updateRow('pharmacy_drugs',id,{quantity:drugs.find(d=>d.id==id)?.qty}).catch(()=>{});
  render();toast(`Dispensed ${qty} units`);
}
</script>