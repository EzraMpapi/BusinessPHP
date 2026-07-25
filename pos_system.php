<div class="space-y-4">
  <div class="section-header"><h2>Point of Sale</h2>
    <button class="btn btn-primary" onclick="openCheckout()"><i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i> New Sale</button></div>
  <div id="pos-kpis" class="grid-4" style="gap:12px">
    <div class="kpi"><div class="spinner"></div></div><div class="kpi"><div class="spinner"></div></div>
    <div class="kpi"><div class="spinner"></div></div><div class="kpi"><div class="spinner"></div></div></div>
  <div id="pos-today"></div>
  <div id="checkout-modal" class="hidden fixed inset-0 bg-black/40 z-50 flex items-end sm:items-center justify-center p-4">
    <div class="card w-full max-w-lg"><div class="flex items-center justify-between mb-4">
      <h3 class="font-bold text-[15px]">New Sale</h3>
      <button onclick="document.getElementById('checkout-modal').classList.add('hidden')" class="text-slate-400"><i data-lucide="x" class="w-4 h-4"></i></button></div>
      <div id="cart" class="space-y-2 mb-4 max-h-48 overflow-y-auto"></div>
      <div class="grid-2" style="gap:8px;margin-bottom:12px">
        <select id="pos-product" class="bs-input"><option value="">Select product</option></select>
        <button class="btn btn-secondary" onclick="addToCart()"><i data-lucide="plus" class="w-3.5 h-3.5"></i> Add</button>
      </div>
      <div class="border-t border-slate-100 pt-3 space-y-2">
        <div class="flex justify-between text-[13px]"><span class="text-slate-500">Subtotal</span><span id="sub-total" class="font-bold">TZS 0</span></div>
        <div class="flex justify-between text-[13px]"><span class="text-slate-500">VAT 18%</span><span id="vat-total" class="font-bold">TZS 0</span></div>
        <div class="flex justify-between text-[15px] font-bold"><span>Total</span><span id="grand-total" class="text-[#16A34A]">TZS 0</span></div>
      </div>
      <div class="grid-2" style="gap:8px;margin-top:12px">
        <select id="pay-method" class="bs-input"><option>Cash</option><option>M-Pesa</option><option>Airtel Money</option><option>Card</option></select>
        <button class="btn btn-primary" onclick="completeSale()"><i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i> Complete Sale</button>
      </div>
    </div>
  </div>
</div>
<script>
let pos_transactions=[];let cart=[];
const PRODUCTS=[
  {id:1,name:'Samsung TV 55"',price:1200000},{id:2,name:'LG Fridge 400L',price:1650000},
  {id:3,name:'Unga Dona 2kg',price:5000},{id:4,name:'Cooking Oil 5L',price:16500},
  {id:5,name:'Cement 50kg',price:19000},{id:6,name:'Iron Sheet 4m',price:36000},
];
(async()=>{
  const rows=await fetchRows('pos_transactions',{},100);
  pos_transactions=rows.length?rows:[
    {id:'POS-501',items:'Unga Dona x5, Cooking Oil x2',amount:58000,method:'M-Pesa',time:'10:22',cashier:'Amina'},
    {id:'POS-500',items:'Samsung TV 55"',amount:1200000,method:'Card',time:'09:45',cashier:'John'},
    {id:'POS-499',items:'Cement 50kg x3',amount:57000,method:'Cash',time:'09:12',cashier:'Amina'},
    {id:'POS-498',items:'Iron Sheet 4m x2, Cement x1',amount:91000,method:'Cash',time:'08:55',cashier:'Grace'},
  ];
  // Populate product select
  const sel=document.getElementById('pos-product');
  PRODUCTS.forEach(p=>{const o=document.createElement('option');o.value=p.id;o.textContent=p.name+' ('+money(p.price)+')';sel.appendChild(o);});
  render();
})();
function render(){
  const total=pos_transactions.reduce((s,t)=>s+(t.amount||0),0);
  const mpesa=pos_transactions.filter(t=>t.method==='M-Pesa').length;
  document.getElementById('pos-kpis').innerHTML=
    buildKpi('Sales Today',String(pos_transactions.length),'+3',true,'#16A34A')+
    buildKpi('Revenue Today',money(total),'+15%',true,'#2563EB')+
    buildKpi('M-Pesa Sales',String(mpesa),'',null,'#16A34A')+
    buildKpi('Avg Transaction',money(pos_transactions.length?total/pos_transactions.length:0),'',null,'#7C3AED');
  document.getElementById('pos-today').innerHTML=buildTable(
    ['Receipt #','Items','Amount','Method','Time','Cashier'],
    pos_transactions,t=>`<tr>
      <td class="font-mono font-semibold text-[#111827]">${t.id||t.receipt_number}</td>
      <td class="text-slate-500">${t.items||t.description||''}</td>
      <td class="font-bold">${money(t.amount)}</td>
      <td>${buildPill(t.method||'Cash',t.method==='M-Pesa'||t.method==='Airtel Money'?'#16A34A':'#2563EB')}</td>
      <td class="text-slate-500">${t.time||t.created_at?.slice(11,16)||''}</td>
      <td class="text-slate-500">${t.cashier||t.cashier_name||'Admin'}</td>
    </tr>`);
  lucide.createIcons();
}
function addToCart(){
  const sel=document.getElementById('pos-product');
  const pid=Number(sel.value);if(!pid){toast('Select a product','error');return;}
  const prod=PRODUCTS.find(p=>p.id===pid);
  const existing=cart.find(c=>c.id===pid);
  if(existing)existing.qty++;else cart.push({...prod,qty:1});
  renderCart();
}
function renderCart(){
  const subtotal=cart.reduce((s,c)=>s+c.price*c.qty,0);
  const vat=Math.round(subtotal*0.18);
  const grand=subtotal+vat;
  document.getElementById('cart').innerHTML=cart.map((c,i)=>`
    <div class="flex items-center justify-between py-1 border-b border-slate-50">
      <span class="text-[12.5px] font-medium">${c.name}</span>
      <div class="flex items-center gap-2">
        <button onclick="cartQty(${i},-1)" class="w-6 h-6 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-[12px]">-</button>
        <span class="text-[12px] font-bold w-5 text-center">${c.qty}</span>
        <button onclick="cartQty(${i},1)" class="w-6 h-6 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-[12px]">+</button>
        <span class="text-[12px] font-semibold w-20 text-right">${money(c.price*c.qty)}</span>
      </div>
    </div>`).join('')||'<p class="text-[12px] text-slate-400 text-center py-4">Cart is empty</p>';
  document.getElementById('sub-total').textContent=money(subtotal);
  document.getElementById('vat-total').textContent=money(vat);
  document.getElementById('grand-total').textContent=money(grand);
}
function cartQty(i,d){cart[i].qty+=d;if(cart[i].qty<=0)cart.splice(i,1);renderCart();}
function openCheckout(){cart=[];renderCart();document.getElementById('checkout-modal').classList.remove('hidden');}
async function completeSale(){
  if(!cart.length){toast('Cart is empty','error');return;}
  const subtotal=cart.reduce((s,c)=>s+c.price*c.qty,0);
  const grand=Math.round(subtotal*1.18);
  const items=cart.map(c=>c.name+(c.qty>1?' x'+c.qty:'')).join(', ');
  const method=document.getElementById('pay-method').value;
  const tx={id:'POS-'+uid(),items,amount:grand,method,time:new Date().toTimeString().slice(0,5),cashier:'Admin'};
  pos_transactions.unshift(tx);cart=[];
  try{await insertRow('pos_transactions',{description:items,amount:grand,payment_method:method});toast('Sale complete! '+money(grand));}catch(e){toast('Sale recorded locally');}
  document.getElementById('checkout-modal').classList.add('hidden');render();
}
</script>