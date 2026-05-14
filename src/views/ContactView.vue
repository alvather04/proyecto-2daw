<template>
  <div class="login-section">
    <div class="login-container">
      <div class="login-header">
        <h2>Contacto</h2>
        <p>¿Tienes alguna pregunta? Contáctanos</p>
      </div>
      <div class="login-form-box">
        <form @submit.prevent="handleSubmit">
          <div class="form-group">
            <label for="name">Nombre</label>
            <input type="text" id="name" v-model="form.name" required placeholder="Tu nombre">
          </div>
          <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" id="email" v-model="form.email" required placeholder="tu@email.com">
          </div>
          <div class="form-group">
            <label for="subject">Asunto</label>
            <input type="text" id="subject" v-model="form.subject" required placeholder="Asunto del mensaje">
          </div>
          <div class="form-group">
            <label for="message">Mensaje</label>
            <textarea id="message" v-model="form.message" required placeholder="Tu mensaje..." rows="5"></textarea>
          </div>
          <p v-if="error" style="color: #dc3545;">{{ error }}</p>
          <p v-if="success" style="color: #4ade80;">{{ success }}</p>
          <button type="submit" class="btn-login">Enviar Mensaje</button>
        </form>
        <div class="login-links">
          <router-link to="/profile" class="register-link">Volver a mi perfil</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ContactView',
  data() {
    return {
      form: {
        name: '',
        email: '',
        subject: '',
        message: ''
      },
      error: '',
      success: ''
    };
  },
  created() {
    const user = localStorage.getItem('nexus_user');
    if (user) {
      const userData = JSON.parse(user);
      this.form.name = userData.username;
      this.form.email = userData.email;
    }
  },
  methods: {
    async handleSubmit() {
      this.error = '';
      this.success = '';
      try {
        const user = JSON.parse(localStorage.getItem('nexus_user') || '{}');
        const response = await fetch('api/contact.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            user_id: user.id,
            name: this.form.name,
            email: this.form.email,
            subject: this.form.subject,
            message: this.form.message
          })
        });
        const data = await response.json();
        
        if (data.success) {
          this.success = 'Mensaje enviado correctamente';
          this.form.subject = '';
          this.form.message = '';
        } else {
          this.error = data.error;
        }
      } catch (e) {
        this.error = 'Error de conexión. Verifica que el servidor esté activo.';
      }
    }
  }
};
</script>