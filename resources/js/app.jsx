import React, { useState, useEffect } from "react";
import ReactDOM from "react-dom/client";

function App() {
  // ambil data dari Laravel (sudah di-preload)
  const [menus, setMenus] = useState(window.__MENUS__ || []);

  // kalau mau refresh data lagi bisa fetch
  useEffect(() => {
    if (menus.length === 0) {
      fetch("/api/menu")
        .then(res => res.json())
        .then(data => setMenus(data));
    }
  }, []);

  return (
    <div>
      <h1>Daftar Menu</h1>
      {menus.map(menu => (
        <div key={menu.id} style={{ marginBottom: "20px" }}>
          <h2>{menu.nama_hidangan}</h2>
          <img
            src={`/storage/${menu.gambar}`}
            alt={menu.nama_hidangan}
            width="200"
          />
          <p>Harga: Rp {menu.harga_jual}</p>
        </div>
      ))}
    </div>
  );
}

ReactDOM.createRoot(document.getElementById("app")).render(<App />);
