<!DOCTYPE html>
<html lang="es-MX">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ventas Tool — Punto de venta multi-caja con facturación CFDI 4.0</title>
  <meta name="description" content="Ventas Tool: sistema punto de venta multi-caja con inventario, clientes, compras y facturación CFDI 4.0 integrada. Desde $79/mes.">
  <link rel="icon" type="image/png" href="./assets/img/favicon.png">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css">
  <link rel="stylesheet" href="./assets/css/css.css">

  <!-- recaptcha Google -->
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>

</head>

<body>

  <header>
    <div class="wrap">
      <nav>
        <a href="#top" aria-label="Ventas Tool — inicio">
          <img class="logo-nav" src="./assets/img/logo.png" alt="Ventas Tool">
        </a>
        <div class="nav-links">
          <a href="#modulos">Módulos</a>
          <a href="#multicaja">Multi-caja</a>
          <a href="#facturacion">Facturación CFDI</a>
          <a href="#precios">Precios</a>
          <a href="#faq">Preguntas</a>
        </div>
        <div class="nav-cta">
          <a href="./app/" class="btn btn-primario" style="padding:10px 20px;font-size:14px;box-shadow:0 4px 0 var(--rojo-oscuro)">Ingresar <i class="fa-solid fa-arrow-right"></i></a>
        </div>
      </nav>
    </div>
  </header>

  <main id="top">

    <!-- HERO -->
    <section class="hero">
      <div class="wrap hero-grid">
        <div>
          <span class="eyebrow">Punto de venta · Multi-caja · CFDI 4.0</span>
          <h1>Controla <span class="subrayado">todas tus cajas</span> y factura sin salir del mostrador</h1>
          <p class="lead">Ventas Tool es el sistema punto de venta para negocios mexicanos: inventario, clientes, compras y timbrado fiscal en un solo lugar, sin importar cuántas cajas tengas abiertas.</p>
          <div class="hero-ctas">
            <a href="#precios" class="btn btn-primario">Ver planes desde $79/mes</a>
            <a href="#modulos" class="btn btn-fantasma">Conocer los módulos</a>
          </div>
          <div class="hero-badges">
            <span class="hero-badge"><span class="punto"></span>Timbrado CFDI 4.0</span>
            <span class="hero-badge"><span class="punto"></span>Sin límite de cajas registradoras</span>
            <span class="hero-badge"><span class="punto"></span>Cancela cuando quieras</span>
          </div>
        </div>

        <div class="impresora">
          <div class="ranura"></div>
          <div class="ticket-caja">
            <span class="t-caja">CAJA 02</span>
            <img class="t-logo" src="./assets/img/logo.png" alt="Ventas Tool">
            <div class="t-linea"></div>
            <div class="t-fila"><span>2x Refresco 600ml</span><b>$36.00</b></div>
            <div class="t-fila"><span>1x Pan de caja</span><b>$42.50</b></div>
            <div class="t-fila"><span>3x Detergente 1L</span><b>$87.00</b></div>
            <div class="t-linea"></div>
            <div class="t-total"><span>TOTAL</span><span>$165.50</span></div>
            <div class="t-sello">✓ CFDI TIMBRADO ANTE SAT</div>
          </div>
        </div>
      </div>
    </section>

    <!-- CONFIANZA -->
    <div class="confianza">
      <div class="wrap">
        <span>ABARROTES</span><span class="sep">·</span>
        <span>FERRETERÍAS</span><span class="sep">·</span>
        <span>DISTRIBUIDORAS</span><span class="sep">·</span>
        <span>PANADERÍAS</span><span class="sep">·</span>
        <span>BOUTIQUES</span><span class="sep">·</span>
        <span>FARMACIAS</span><span class="sep">·</span>
        <span>MISCELÁNEAS</span>
      </div>
    </div>

    <!-- MÓDULOS -->
    <section id="modulos">
      <div class="wrap">
        <div class="modulos-cabecera reveal">
          <span class="eyebrow">Todo el sistema</span>
          <h2>Un módulo para cada parte del negocio</h2>
          <p>Desde el producto que compras hasta el ticket que entregas y la factura que se timbra: Ventas Tool conecta cada paso para que no captures nada dos veces.</p>
        </div>

        <div class="grupo reveal">
          <div class="grupo-titulo"><span class="num">01</span>
            <h3>Producto</h3>
            <div class="linea"></div>
          </div>
          <div class="tarjetas">
            <div class="tarjeta">
              <div class="icono"><i class="fa-solid fa-box-archive"></i></div>
              <h4>Productos</h4>
              <p>Catálogo con precios, existencias y fotos, listo para vender en cualquier caja del negocio.</p>
            </div>
            <div class="tarjeta">
              <div class="icono"><i class="fa-solid fa-clipboard"></i></div>
              <h4>Inventario</h4>
              <p>Existencias en tiempo real por sucursal, con alertas antes de que un producto se agote.</p>
            </div>
            <div class="tarjeta">
              <div class="icono"><i class="fa-solid fa-list"></i></div>
              <h4>Clasificaciones</h4>
              <p>Organiza tu catálogo por categorías y familias para encontrar cualquier producto en segundos.</p>
            </div>
          </div>
        </div>

        <div class="grupo reveal">
          <div class="grupo-titulo"><span class="num">02</span>
            <h3>Ventas</h3>
            <div class="linea"></div>
          </div>
          <div class="tarjetas">
            <div class="tarjeta">
              <div class="icono"><i class="fa-solid fa-users"></i></div>
              <h4>Clientes</h4>
              <p>Historial de compras, datos fiscales y crédito por cliente, disponible desde cualquier caja.</p>
            </div>
            <div class="tarjeta">
              <div class="icono"><i class="fa-solid fa-receipt"></i></div>
              <h4>Ventas</h4>
              <p>Cobra en efectivo, tarjeta o crédito, aplica descuentos y cierra el ticket en segundos.</p>
            </div>
            <div class="tarjeta">
              <div class="icono"><i class="fa-solid fa-cash-register"></i></div>
              <h4>Cajas</h4>
              <p>Abre, corta y concilia tantas cajas registradoras como necesite tu negocio, todas sincronizadas.</p>
            </div>
          </div>
        </div>

        <div class="grupo reveal">
          <div class="grupo-titulo"><span class="num">03</span>
            <h3>Compras</h3>
            <div class="linea"></div>
          </div>
          <div class="tarjetas">
            <div class="tarjeta">
              <div class="icono"><i class="fa-solid fa-truck"></i></div>
              <h4>Proveedores</h4>
              <p>Directorio de proveedores con historial de compras y condiciones de pago acordadas.</p>
            </div>
            <div class="tarjeta">
              <div class="icono"><i class="fa-solid fa-bag-shopping"></i></div>
              <h4>Compras</h4>
              <p>Registra entradas de mercancía y actualiza el inventario automáticamente al recibirla.</p>
            </div>
          </div>
        </div>

        <div class="grupo reveal" style="margin-bottom:0">
          <div class="grupo-titulo"><span class="num">04</span>
            <h3>Otros</h3>
            <div class="linea"></div>
          </div>
          <div class="tarjetas">
            <div class="tarjeta">
              <div class="icono"><i class="fa-solid fa-file-lines"></i></div>
              <h4>Gastos</h4>
              <p>Registra los gastos del negocio para saber exactamente cuánto queda de tu utilidad.</p>
            </div>
            <div class="tarjeta">
              <div class="icono"><i class="fa-solid fa-user-tie"></i></div>
              <h4>Usuarios</h4>
              <p>Da acceso a tu equipo con permisos por rol: cajero, encargado o administrador.</p>
            </div>
            <div class="tarjeta">
              <div class="icono"><i class="fa-solid fa-chart-line"></i></div>
              <h4>Reportes</h4>
              <p>Ventas por caja, por producto o por vendedor, listos para revisar cuando los necesites.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- MULTI-CAJA -->
    <section id="multicaja">
      <div class="wrap">
        <div class="multicaja reveal">
          <div class="multicaja-grid">
            <div>
              <span class="eyebrow">Multi-caja</span>
              <h2>Cada caja vende por su cuenta. Tú lo ves todo en un solo lugar.</h2>
              <p>Abre cuantas cajas registradoras necesite tu negocio, mañana, tarde o en distintas sucursales. Cada cajero corta su turno de forma independiente y tú concilias todo desde un solo panel.</p>
              <ul class="lista-check">
                <li>Apertura y corte de caja por turno y por cajero</li>
                <li>Ventas y cobros simultáneos sin bloquear el sistema</li>
                <li>Conciliación de efectivo, tarjeta y crédito por caja</li>
                <li>Historial de movimientos por caja para auditar cualquier turno</li>
              </ul>
              <a href="#precios" class="btn btn-primario">Empezar con mis cajas</a>
            </div>
            <div class="cajas-mini">
              <div class="caja-mini">
                <div class="izq"><span class="punto-activo"></span>
                  <div>
                    <div class="nombre">Caja 01 — Mostrador</div>
                    <div class="sub">Turno matutino · Ana R.</div>
                  </div>
                </div>
                <div class="monto">$4,280.00</div>
              </div>
              <div class="caja-mini">
                <div class="izq"><span class="punto-activo"></span>
                  <div>
                    <div class="nombre">Caja 02 — Bodega</div>
                    <div class="sub">Turno vespertino · Luis M.</div>
                  </div>
                </div>
                <div class="monto">$2,915.50</div>
              </div>
              <div class="caja-mini">
                <div class="izq"><span class="punto-activo"></span>
                  <div>
                    <div class="nombre">Caja 03 — Sucursal Centro</div>
                    <div class="sub">Turno matutino · Karla P.</div>
                  </div>
                </div>
                <div class="monto">$6,102.00</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FACTURACIÓN CFDI -->
    <section id="facturacion">
      <div class="wrap cfdi">
        <div class="reveal">
          <span class="eyebrow">Facturación electrónica</span>
          <h2>Factura CFDI 4.0 sin salir del punto de venta</h2>
          <p class="lead">Cada venta puede timbrarse ante el SAT desde la misma pantalla de cobro. Configura tus impuestos una sola vez y deja que el sistema arme el comprobante fiscal por ti.</p>
          <div class="cfdi-chips">
            <span class="chip">CFDI 4.0</span>
            <span class="chip">Facturas</span>
            <span class="chip">Configuración fiscal</span>
            <span class="chip">Catálogo de impuestos</span>
            <span class="chip">Timbrado con PAC</span>
          </div>
        </div>
        <div class="factura-mock reveal">
          <div class="fila-top">
            <div>
              <div style="font-weight:700;font-size:16px">Factura CFDI 4.0</div>
              <div class="folio">Folio fiscal 8F2A-91C4-…</div>
            </div>
            <span class="estado">TIMBRADA</span>
          </div>
          <div class="campos-grid">
            <div class="campo"><label>Receptor</label>
              <div class="val">Miscelánea Ríos S.A. de C.V.</div>
            </div>
            <div class="campo"><label>Uso CFDI</label>
              <div class="val">G03 · Gastos en general</div>
            </div>
            <div class="campo"><label>Método de pago</label>
              <div class="val">PUE · Una exhibición</div>
            </div>
            <div class="campo"><label>Forma de pago</label>
              <div class="val">01 · Efectivo</div>
            </div>
          </div>
          <div class="divisor"></div>
          <div class="campo">
            <label>Total con impuestos</label>
            <div class="val" style="font-family:var(--mono);font-size:22px">$1,922.00</div>
          </div>
        </div>
      </div>
    </section>

    <!-- PARA TU NEGOCIO -->
    <section class="negocios">
      <div class="wrap">
        <div class="negocios-cabecera reveal">
          <span class="eyebrow">Para negocios como el tuyo</span>
          <h2>Un mismo sistema, para el giro que tengas</h2>
        </div>
        <div class="negocios-lista reveal">
          <div class="negocio-chip"><span class="em">🧺</span>
            <h4>Abarrotes y misceláneas</h4>
            <p>Control de existencias que se mueven rápido.</p>
          </div>
          <div class="negocio-chip"><span class="em">🔩</span>
            <h4>Ferreterías</h4>
            <p>Catálogos grandes, organizados por clasificación.</p>
          </div>
          <div class="negocio-chip"><span class="em">🚚</span>
            <h4>Distribuidoras</h4>
            <p>Compras y proveedores conectados al inventario.</p>
          </div>
          <div class="negocio-chip"><span class="em">👗</span>
            <h4>Boutiques</h4>
            <p>Ventas ágiles con clientes y crédito controlado.</p>
          </div>
          <div class="negocio-chip"><span class="em">💊</span>
            <h4>Farmacias</h4>
            <p>Múltiples cajas con corte independiente por turno.</p>
          </div>
          <div class="negocio-chip"><span class="em">🥖</span>
            <h4>Panaderías</h4>
            <p>Cobro rápido en mostrador y reportes al cierre.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- PRECIOS -->
    <section id="precios">
      <div class="wrap">
        <div class="precios-cabecera reveal">
          <span class="eyebrow">Precios</span>
          <h2>Regístrate ahora y obtén una semana de prueba gratis, sin compromiso.</h2>
          <p>Todos los módulos incluidos desde el primer día: ventas, inventario, compras, multi-caja y facturación CFDI 4.0.</p>
          <h4>Un solo plan. Paga como te convenga.</h4>
        </div>

        <div class="toggle-wrap reveal">
          <span class="toggle-label activo" id="labelMensual">Mensual</span>
          <button class="switch" id="switchPlan" role="switch" aria-checked="false" aria-label="Cambiar entre pago mensual y anual"></button>
          <span class="toggle-label" id="labelAnual">Anual</span>
          <span class="ahorro-tag">Ahorra 20%</span>
        </div>

        <div class="row reveal justify-content-center">
          <!-- PLANES -->
          <div class="col-md-5">
            <div class="ticket-precio">
              <div class="marca">Ventas Tool · Recibo de suscripción</div>
              <div class="plan-nombre">Plan Multi-caja</div>
              <div class="t-linea"></div>
              <div class="precio-grande">
                <span class="num"><sup>$</sup><span id="precioNum">99</span></span>
                <span class="periodo" id="precioPeriodo">MXN / mes, cobrado mensualmente</span>
              </div>
              <div class="nota-anual" id="notaAnual">&nbsp;</div>
              <div class="t-linea"></div>
              <ul>
                <li><span class="chk">✓</span> <b>Cajas registradoras</b> ilimitadas</li>
                <li><span class="chk">✓</span> <b>Productos e inventario</b> sin límite</li>
                <li><span class="chk">✓</span> <b>Clientes y compras</b> incluidos</li>
                <li><span class="chk">✓</span> <b>Facturación CFDI 4.0</b> con timbrado</li>
                <li><span class="chk">✓</span> <b>Usuarios</b> con permisos por rol</li>
                <li><span class="chk">✓</span> <b>Reportes</b> por caja y vendedor</li>
              </ul>

              <div class="barras"></div>
              <div class="t-sello" id="selloPrecio">*** GRACIAS POR SU PREFERENCIA ***</div>
            </div>
          </div>

          <!-- Vista: formulario -->
          <div class="col-md-6">
            <div id="vistaFormulario" class="p-3">
              <h3 id="modalRegistroLabel">Crea tu cuenta</h3>
              <p class="modal-sub">Configura tu punto de venta en unos minutos.</p>
              <span class="modal-gratis">Regístrate sin costo · 1 semana de prueba gratis</span>

              <form id="formRegistro" novalidate>
                <div class="mb-3">
                  <label for="regNombre" class="form-label">Nombre completo</label>
                  <input type="text" class="form-control" id="regNombre" name="regNombre" placeholder="Ej. Juan Ramírez" required>
                  <div class="invalid-feedback">Escribe tu nombre.</div>
                </div>

                <div class="mb-3">
                  <label for="regNegocio" class="form-label">Tipo de negocio</label>
                  <select class="form-select" id="regNegocio" name="regNegocio" required>
                    <option value="" selected disabled>Selecciona una opción</option>
                    <option value="abarrotes">Tienda de abarrotes / miscelánea</option>
                    <option value="farmacia">Farmacia</option>
                    <option value="ferreteria">Ferretería</option>
                    <option value="boutique">Boutique / ropa</option>
                    <option value="panaderia">Panadería</option>
                    <option value="distribuidora">Distribuidora</option>
                    <option value="otro">Otro</option>
                  </select>
                  <div class="invalid-feedback">Selecciona el tipo de negocio.</div>
                </div>

                <div class="mb-3" id="grupoOtroNegocio" style="display:none">
                  <label for="regNegocioOtro" class="form-label">¿Cuál es tu giro?</label>
                  <input type="text" class="form-control" id="regNegocioOtro" name="regNegocioOtro" placeholder="Cuéntanos a qué se dedica tu negocio">
                </div>

                <div class="mb-3">
                  <label for="regCorreo" class="form-label">Correo electrónico</label>
                  <input type="email" class="form-control" id="regCorreo" name="regCorreo" placeholder="tucorreo@ejemplo.com" required>
                  <div class="invalid-feedback">Escribe un correo válido.</div>
                </div>

                <div class="mb-3">
                  <label for="regPassword" class="form-label">Contraseña</label>
                  <input type="password" class="form-control" id="regPassword" name="regPassword" placeholder="Mínimo 8 caracteres" minlength="8" required>
                  <div class="invalid-feedback">La contraseña debe tener al menos 8 caracteres.</div>
                </div>

                <div class="mb-3">
                  <label for="regPassword2" class="form-label">Repite tu contraseña</label>
                  <input type="password" class="form-control" id="regPassword2" name="regPassword2" placeholder="Vuelve a escribirla" required>
                  <div class="invalid-feedback" id="errorCoinciden">Las contraseñas no coinciden.</div>
                </div>

                <div class="form-group col-md-5 pb-2">
                  <input type="hidden" name="accion" id="accion" value="registro">
                  <div class="g-recaptcha" data-sitekey="6LcsPXktAAAAAJDtlyMUNVHz4yu29ED6_jrh1lnJ"></div>
                </div>

                <button type="submit" class="btn-registro" id="bBotonRegistar">Crear cuenta gratis <i class="fas fa-save"></i></button>
                <p class="modal-nota" style="text-decoration: underline; cursor: pointer;" data-bs-toggle="modal" data-bs-target="#modalLegal" data-bs-legal-tab="privacidad">Al registrarte aceptas nuestros Términos y Aviso de privacidad.</p>
              </form>
            </div>
          </div>

        </div>
      </div>
    </section>

    <!-- FAQ -->
    <section id="faq">
      <div class="wrap">
        <div class="faq-cabecera reveal">
          <span class="eyebrow">Preguntas frecuentes</span>
          <h2>Antes de que preguntes</h2>
        </div>
        <div class="faq-lista reveal">
          <details class="faq-item" open>
            <summary class="faq-pregunta">¿Cuántas cajas puedo tener con un solo plan?<span class="mas">+</span></summary>
            <div class="faq-respuesta">Las que necesite tu negocio. El plan Multi-caja no cobra por caja registradora adicional ni por sucursal: puedes abrir todas las que uses en el día a día.</div>
          </details>
          <details class="faq-item">
            <summary class="faq-pregunta">¿La facturación CFDI 4.0 tiene costo aparte?<span class="mas">+</span></summary>
            <div class="faq-respuesta">Sí, el sistema incluye la facturación CFDI 4.0, pero para el timbrado ante el SAT debes comprar los timbres; hay diferentes paquetes según tus necesidades. Por favor pregunta a soporte.</div>
          </details>
          <details class="faq-item">
            <summary class="faq-pregunta">¿Qué diferencia hay entre pagar mensual o anual?<span class="mas">+</span></summary>
            <div class="faq-respuesta">El precio y las funciones son las mismas. Pagando el año completo, el costo baja de $99 a $79 al mes, es decir, $948 MXN al año en lugar de $1,188 MXN.</div>
          </details>
          <details class="faq-item">
            <summary class="faq-pregunta">¿Puedo dar acceso a mis cajeros y encargados?<span class="mas">+</span></summary>
            <div class="faq-respuesta">Sí, el módulo de Usuarios permite crear cuentas por persona y asignar permisos.</div>
          </details>
          <details class="faq-item">
            <summary class="faq-pregunta">¿Puedo cancelar mi suscripción cuando quiera?<span class="mas">+</span></summary>
            <div class="faq-respuesta">Sí. No hay contrato forzoso; puedes cancelar en cualquier momento desde tu cuenta y seguirás teniendo acceso hasta el fin del periodo ya pagado.</div>
          </details>
        </div>
      </div>
    </section>

    <!-- CTA FINAL -->
    <section id="contacto">
      <div class="wrap">
        <div class="cta-final reveal">
          <h2>Pon a trabajar todas tus cajas hoy mismo</h2>
          <p>Configura tu punto de venta, conecta tus cajas registradoras y empieza a facturar en el mismo día.</p>
          <div class="hero-ctas">
            <a href="https://wa.me/523481167983?text=Hola%2C%20quiero%20información%20sobre%20Ventas%20Tool" target="_blank" class="btn btn-fantasma">Hablar con ventas</a>
          </div>
        </div>
      </div>
    </section>

  </main>

  <footer>
    <div class="wrap">
      <div class="footer-grid">
        <div>
          <img class="footer-logo" src="./assets/img/logo.png" alt="Ventas Tool">
          <p>Punto de venta multi-caja con facturación CFDI 4.0 para negocios en México.</p>
        </div>
        <div class="footer-col">
          <h5>Sistema</h5>
          <a href="#modulos">Módulos</a>
          <a href="#multicaja">Multi-caja</a>
          <a href="#facturacion">Facturación CFDI</a>
          <a href="#precios">Precios</a>
        </div>
        <div class="footer-col">
          <h5>Soporte</h5>
          <a href="#faq">Preguntas frecuentes</a>
          <a href="#contacto">Contacto</a>
          <a href="#">Estado del servicio</a>
        </div>
        <div class="footer-col">
          <h5>Legal</h5>
          <a href="#" data-bs-toggle="modal" data-bs-target="#modalLegal" data-bs-legal-tab="terminos">Términos de servicio</a>
          <a href="#" data-bs-toggle="modal" data-bs-target="#modalLegal" data-bs-legal-tab="privacidad">Aviso de privacidad</a>
        </div>
      </div>
      <div class="footer-bottom">
        <span>© 2026 Ventas Tool. Todos los derechos reservados.</span>
        <span>Hecho para negocios en México · CFDI 4.0</span>
      </div>
    </div>
  </footer>

  <a href="https://wa.me/523481167983?text=Hola%2C%20quiero%20información%20sobre%20Ventas%20Tool"
    target="_blank"
    rel="noopener noreferrer"
    class="btn-whatsapp">
    <i class="fa-brands fa-whatsapp"></i>
    Escríbenos por WhatsApp
  </a>

  <div class="modal fade" id="modalLegal" tabindex="-1" aria-labelledby="modalLegalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <div style="width:100%">
            <div class="d-flex justify-content-between align-items-start">
              <h3 id="modalLegalLabel">Información legal</h3>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <ul class="nav nav-tabs" id="legalTabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="tab-terminos-btn" data-bs-toggle="tab" data-bs-target="#tab-terminos" type="button" role="tab">Términos y condiciones</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="tab-privacidad-btn" data-bs-toggle="tab" data-bs-target="#tab-privacidad" type="button" role="tab">Aviso de privacidad</button>
              </li>
            </ul>
          </div>
        </div>

        <div class="modal-body">
          <div class="tab-content">

            <!-- TÉRMINOS Y CONDICIONES -->
            <div class="tab-pane fade show active legal-contenido" id="tab-terminos" role="tabpanel">
              <span class="legal-fecha">Última actualización: agosto de 2026</span>

              <h4>1. Sobre el servicio</h4>
              <p>Ventas Tool es un sistema de punto de venta en línea que permite administrar productos, inventario, clientes, compras, ventas en una o varias cajas registradoras y facturación electrónica CFDI 4.0. Al crear una cuenta y usar Ventas Tool, aceptas estos Términos y Condiciones.</p>

              <h4>2. Registro y cuenta</h4>
              <p>Debes proporcionar información veraz al registrarte. Eres responsable de mantener la confidencialidad de tu contraseña y de toda la actividad que ocurra dentro de tu cuenta y de las cuentas de los usuarios que tú des de alta (cajeros, encargados, administradores).</p>

              <h4>3. Prueba gratuita y suscripción</h4>
              <ul>
                <li>El registro es gratuito y otorga un periodo de prueba de 7 días con acceso completo al sistema.</li>
                <li>Al finalizar la prueba, continuar usando Ventas Tool requiere una suscripción de pago, mensual o anual, con los precios vigentes publicados en el sitio.</li>
                <li>Las suscripciones se renuevan automáticamente al final de cada periodo, salvo cancelación previa por parte del usuario.</li>
                <li>Puedes cancelar tu suscripción en cualquier momento; el acceso se mantiene hasta el fin del periodo ya pagado, sin reembolsos por el tiempo no utilizado salvo que la ley aplicable indique lo contrario.</li>
              </ul>

              <h4>4. Facturación electrónica (CFDI)</h4>
              <p>Para timbrar comprobantes fiscales, Ventas Tool se conecta con un Proveedor Autorizado de Certificación (PAC) externo. Es responsabilidad del usuario proporcionar información fiscal correcta (RFC, régimen, uso de CFDI, certificados) y contar con los timbres o el saldo necesario para el timbrado. Ventas Tool no es responsable por errores derivados de información fiscal incorrecta proporcionada por el usuario, ni por interrupciones del servicio del PAC o del SAT ajenas a su control.</p>

              <h4>5. Uso permitido</h4>
              <p>Te comprometes a usar el sistema conforme a la ley, sin intentar vulnerar su seguridad, sin usarlo para fines fraudulentos y sin compartir tu acceso con terceros no autorizados por tu organización.</p>

              <h4>6. Disponibilidad del servicio</h4>
              <p>Procuramos que el servicio esté disponible de forma continua, pero puede haber interrupciones por mantenimiento, causas de fuerza mayor o fallas de terceros (hosting, PAC, conectividad). Ventas Tool no garantiza disponibilidad ininterrumpida al 100%.</p>

              <h4>7. Propiedad intelectual</h4>
              <p>El software, el diseño, la marca Ventas Tool y sus elementos gráficos son propiedad de sus titulares. El usuario conserva la propiedad de los datos de su negocio (productos, ventas, clientes) que ingresa al sistema.</p>

              <h4>8. Limitación de responsabilidad</h4>
              <p>Ventas Tool se ofrece "tal cual". En la medida permitida por la ley, no somos responsables por pérdidas indirectas, de utilidades o de datos derivadas del uso o la imposibilidad de uso del sistema.</p>

              <h4>9. Cambios a estos términos</h4>
              <p>Podemos actualizar estos Términos y Condiciones. Los cambios relevantes se notificarán a través del sitio o por correo electrónico. El uso continuado del servicio después de una actualización implica la aceptación de los nuevos términos.</p>

              <h4>10. Contacto</h4>
              <p>Para dudas sobre estos términos, escríbenos a <a href="mailto:ventastool@bigtool.mx">ventastool@bigtool.mx</a>.</p>
            </div>

            <!-- AVISO DE PRIVACIDAD -->
            <div class="tab-pane fade legal-contenido" id="tab-privacidad" role="tabpanel">
              <span class="legal-fecha">Última actualización: agosto de 2026</span>

              <h4>1. Responsable de los datos</h4>
              <p>Ventas Tool (en adelante, "nosotros") es responsable del tratamiento de los datos personales que nos proporcionas al registrarte y usar el sistema, conforme a la legislación mexicana en materia de protección de datos personales.</p>

              <h4>2. Datos que recabamos</h4>
              <ul>
                <li>Datos de contacto: nombre, correo electrónico, tipo de negocio.</li>
                <li>Datos de acceso: contraseña (almacenada de forma cifrada), historial de inicio de sesión.</li>
                <li>Datos operativos de tu negocio: productos, inventario, clientes, ventas, compras, cajas y usuarios que tú mismo registras en el sistema.</li>
                <li>Datos fiscales, si activas la facturación: RFC, razón social, régimen fiscal, domicilio fiscal y certificados de sello digital (CSD), necesarios para timbrar comprobantes ante el SAT.</li>
                <li>Datos de pago de la suscripción, procesados por nuestro proveedor de pagos; Ventas Tool no almacena directamente los datos completos de tarjetas de crédito o débito.</li>
              </ul>

              <h4>3. Finalidades del tratamiento</h4>
              <ul>
                <li>Crear y administrar tu cuenta y las cuentas de tus usuarios.</li>
                <li>Operar las funciones del sistema: ventas, inventario, cajas, clientes, compras y reportes.</li>
                <li>Timbrar comprobantes fiscales electrónicos a través de nuestro Proveedor Autorizado de Certificación (PAC).</li>
                <li>Procesar el cobro de tu suscripción mensual o anual.</li>
                <li>Enviarte notificaciones del servicio (confirmaciones, avisos de vencimiento, soporte).</li>
                <li>Mejorar el sistema y prevenir usos indebidos o fraudulentos.</li>
              </ul>

              <h4>4. Transferencia de datos</h4>
              <p>Compartimos datos con terceros únicamente cuando es necesario para operar el servicio, por ejemplo: nuestro Proveedor Autorizado de Certificación (PAC) para el timbrado de CFDI, nuestro proveedor de hospedaje (hosting) y nuestro procesador de pagos. Estos terceros están obligados a proteger tu información y usarla solo para los fines encomendados. No vendemos tus datos personales a terceros.</p>

              <h4>5. Derechos ARCO</h4>
              <p>Tienes derecho a Acceder, Rectificar, Cancelar tus datos personales, u Oponerte a su tratamiento (derechos ARCO), así como a revocar tu consentimiento en cualquier momento. Para ejercer estos derechos, escríbenos a <a href="mailto:ventastool@bigtool.mx">ventastool@bigtool.mx</a> indicando tu solicitud; te responderemos en un plazo razonable conforme a la ley aplicable.</p>

              <h4>6. Conservación de datos</h4>
              <p>Conservamos tus datos mientras tu cuenta permanezca activa y, posteriormente, durante el tiempo necesario para cumplir obligaciones legales, fiscales o contables, o para atender aclaraciones.</p>

              <h4>7. Seguridad</h4>
              <p>Aplicamos medidas técnicas y administrativas razonables para proteger tus datos, incluyendo el cifrado de contraseñas y conexiones seguras. Ningún sistema es 100% infalible; te recomendamos usar contraseñas robustas y no compartir tus credenciales.</p>

              <h4>8. Cambios a este aviso</h4>
              <p>Podemos actualizar este Aviso de Privacidad. Cualquier cambio relevante se publicará en esta misma sección con su fecha de actualización.</p>

              <h4>9. Contacto</h4>
              <p>Si tienes dudas sobre el tratamiento de tus datos personales, contáctanos en <a href="mailto:ventastool@bigtool.mx">ventastool@bigtool.mx</a>.</p>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Entendido</button>
        </div>
      </div>
    </div>
  </div>

  <script src="./assets/js/jquery-4.0.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script src="./assets/js/main.js"></script>

</body>

</html>