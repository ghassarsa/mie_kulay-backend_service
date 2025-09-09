export default function Menu({ menus }) {
  return (
    <div>
      <h1>Daftar Menu</h1>
      {menus.map(menu => <p key={menu.id}>{menu.nama_hidangan}</p>)}
    </div>
  );
}
