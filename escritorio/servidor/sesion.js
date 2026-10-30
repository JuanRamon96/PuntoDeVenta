// Sesión del usuario que inició en el equipo (como $_SESSION['user_punto_venta']). Vive mientras el programa esté abierto.
let actual = null;

// Permisos guardados como "Modulo,ver,crear,editar,eliminar~Modulo,..."
function leerPermisos(texto) {
  const permisos = {};
  String(texto || '').split('~').filter(Boolean).forEach((m) => {
    const partes = m.split(',');
    permisos[partes[0]] = partes;
  });
  return permisos;
}

module.exports = {
  iniciar(usuario) {
    actual = { ...usuario, PermisosLista: leerPermisos(usuario.Permisos) };
  },
  cerrar() {
    actual = null;
  },
  usuario() {
    return actual;
  },
  id() {
    return actual ? Number(actual.ID_Usuario) : 0;
  },
  nombre() {
    return actual ? [actual.Nombre, actual.Primer_Apellido, actual.Segundo_Apellido].join(' ') : '';
  },
  esAdmin() {
    return !!actual && String(actual.Tipo_Usuario) === '1';
  },
  // Igual que en c_controller: $permisos['Modulo'][indice] == '1'
  puede(modulo, indice = 1) {
    return this.esAdmin() || (!!actual && actual.PermisosLista[modulo] && actual.PermisosLista[modulo][indice] === '1');
  }
};
