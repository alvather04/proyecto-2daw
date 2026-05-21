<template>
  <!-- Foro estilo Reddit con categorías, publicaciones, votos y ventana de detalle -->
  <div class="forum-section">
    <div class="forum-main-container">

      <!-- ============================================================ -->
      <!-- BARRA LATERAL IZQUIERDA (filtros y categorías)               -->
      <!-- ============================================================ -->
      <aside class="reddit-sidebar">
        <div class="sidebar-section">
          <h3>Filtros</h3>
          <ul class="sidebar-menu">
            <li><a href="#" class="sidebar-link" :class="{ active: currentFilter === 'home' }" @click.prevent="currentFilter = 'home'">🏠 Inicio</a></li>
            <li><a href="#" class="sidebar-link" :class="{ active: currentFilter === 'saved' }" @click.prevent="showSaved">⭐ Guardados</a></li>
          </ul>
        </div>

        <div class="sidebar-section">
          <h3>Categorías</h3>
          <ul class="sidebar-menu">
            <li><a href="#" class="sidebar-link" :class="{ active: selectedCategory === null && currentFilter === 'home' }" @click.prevent="selectedCategory = null; currentFilter = 'home'">Todos</a></li>
            <li><a href="#" class="sidebar-link" :class="{ active: selectedCategory === 'Estrategia' }" @click.prevent="selectedCategory = 'Estrategia'">Estrategia</a></li>
            <li><a href="#" class="sidebar-link" :class="{ active: selectedCategory === 'Campeones' }" @click.prevent="selectedCategory = 'Campeones'">Campeones</a></li>
            <li><a href="#" class="sidebar-link" :class="{ active: selectedCategory === 'Parches' }" @click.prevent="selectedCategory = 'Parches'">Parches</a></li>
            <li><a href="#" class="sidebar-link" :class="{ active: selectedCategory === 'Competitivo' }" @click.prevent="selectedCategory = 'Competitivo'">Competitivo</a></li>
            <li><a href="#" class="sidebar-link" :class="{ active: selectedCategory === 'General' }" @click.prevent="selectedCategory = 'General'">General</a></li>
          </ul>
        </div>

        <div class="sidebar-section">
          <button class="btn-create-community">+ Crear Comunidad</button>
        </div>
      </aside>

      <!-- ============================================================ -->
      <!-- CONTENIDO PRINCIPAL                                          -->
      <!-- ============================================================ -->
      <main class="reddit-main">

        <!-- Barra para crear una publicación nueva -->
        <div class="create-post-bar">
          <img :src="userAvatar" alt="Avatar" class="user-avatar-small">
          <input v-model="newPostTitle" type="text" placeholder="Título del post..." class="post-input-bar" style="margin-right: 5px;">
          <input v-model="newPostText" type="text" placeholder="¿Qué tienes en mente?" class="post-input-bar" @keyup.enter="createPost">
          <select v-model="newPostCategory" class="post-input-bar" style="margin-right: 5px; width: 120px;">
            <option value="">Categoría</option>
            <option value="Estrategia">Estrategia</option>
            <option value="Campeones">Campeones</option>
            <option value="Parches">Parches</option>
            <option value="Competitivo">Competitivo</option>
            <option value="General">General</option>
          </select>
          <button class="btn-post-bar" @click="createPost">Publicar</button>
        </div>

        <div v-if="loading" style="text-align: center; padding: 20px;">Cargando...</div>

        <!-- ============================================================ -->
        <!-- LISTA DE PUBLICACIONES                                      -->
        <!-- ============================================================ -->
        <div class="reddit-posts-list">
          <div v-for="post in filteredPosts" :key="post.id" class="reddit-post">

            <!-- Columna de votos (arriba/abajo) -->
            <div class="post-votes-side">
              <button class="vote-arrow upvote" :class="{ voted: userVotes[post.id] === 'up' }" @click="vote(post.id, 'up')">▲</button>
              <span class="votes-count">{{ post.vote_count || post.votes }}</span>
              <button class="vote-arrow downvote" :class="{ voted: userVotes[post.id] === 'down' }" @click="vote(post.id, 'down')">▼</button>
            </div>

            <!-- Miniatura -->
            <div class="post-thumbnail post-click-area" @click="openModal(post.id)">
              <img :src="post.image || userAvatar" alt="Post thumbnail">
            </div>

            <!-- Cuerpo de la publicación -->
            <div class="post-body">
              <div class="post-meta">
                <span class="post-subreddit">r/nexushub</span>
                <span class="meta-dot">•</span>
                <span class="post-author">u/{{ post.username }}</span>
                <span class="meta-dot">•</span>
                <span class="post-time">{{ formatTime(post.created_at) }}</span>
                <span :class="['tag', post.tag.toLowerCase()]">{{ post.tag }}</span>
              </div>
              <h2 class="post-title post-click-area" @click="openModal(post.id)">{{ post.title }}</h2>
              <p class="post-description">{{ post.description }}</p>

              <!-- Botones de la publicación -->
              <div class="post-footer">
                <button class="post-action comments-btn" @click="openModal(post.id)">
                  <span class="comment-icon">💬</span>
                  <span class="comment-count">{{ post.comment_count || post.comments || 0 }}</span>
                  <span class="comment-label">Comentarios</span>
                </button>
                <button class="post-action share-btn" @click="sharePost(post)">
                  <span>🔄</span>
                  <span>Compartir</span>
                </button>
                <button class="post-action save-btn" :class="{ saved: savedPosts.has(post.id) }" @click="toggleSave(post.id)">
                  <span>⭐</span>
                  <span>Guardar</span>
                </button>
                <button class="post-action more-btn" @click="deletePost(post.id)" v-if="post.user_id === currentUserId">
                  <span>🗑️</span>
                  <span>Eliminar</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>

    <!-- ============================================================ -->
    <!-- VENTANA DE DETALLE (modal)                                   -->
    <!-- ============================================================ -->
    <div id="post-modal" class="post-modal" :class="{ active: modalOpen }">
      <div class="modal-overlay" @click="closeModal"></div>
      <div class="modal-content">
        <button class="modal-close" @click="closeModal">✕</button>
        <div class="modal-post-detail" v-if="selectedPost">
          <div class="modal-post-header">
            <div class="modal-user-info">
              <img :src="selectedPost.image || userAvatar" alt="Avatar" class="modal-avatar">
              <div class="modal-user-details">
                <span class="modal-username">u/{{ selectedPost.username }}</span>
                <span class="modal-subreddit">{{ selectedPost.subreddit }}</span>
                <span class="modal-time">{{ formatTime(selectedPost.created_at) }}</span>
              </div>
            </div>
            <span :class="['modal-tag', 'tag', selectedPost.tag.toLowerCase()]">{{ selectedPost.tag }}</span>
          </div>

          <h2 class="modal-title">{{ selectedPost.title }}</h2>
          <p class="modal-description">{{ selectedPost.description }}</p>

          <div class="modal-image-container" v-if="selectedPost.image">
            <img :src="selectedPost.image" alt="Post content" class="modal-image">
          </div>

          <div class="modal-stats">
            <span class="modal-votes">{{ selectedPost.vote_count || selectedPost.votes }} votos</span>
            <span class="modal-sep">•</span>
            <span class="modal-comments-count">{{ selectedPost.comment_count || 0 }} comentarios</span>
          </div>

          <!-- Botones dentro del detalle -->
          <div class="modal-actions">
            <button class="modal-action modal-upvote" :class="{ voted: userVotes[selectedPost.id] === 'up' }" @click="vote(selectedPost.id, 'up')">▲ Votar</button>
            <button class="modal-action modal-comment" @click="focusCommentInput">💬 Comentar</button>
            <button class="modal-action modal-share" @click="sharePost(selectedPost)">🔄 Compartir</button>
            <button class="modal-action modal-save" :class="{ saved: savedPosts.has(selectedPost.id) }" @click="toggleSave(selectedPost.id)">⭐ Guardar</button>
          </div>

          <!-- Sección de comentarios -->
          <div class="modal-comments-section">
            <h3>Comentarios ({{ selectedPost.comment_count || 0 }})</h3>
            <div class="comment-input-box">
              <input ref="commentInput" type="text" placeholder="Escribe un comentario..." class="comment-input" v-model="newComment" @keyup.enter="addComment">
              <button class="btn-comment-send" @click="addComment">Enviar</button>
            </div>
            <div class="comments-list">
              <div v-for="comment in postComments" :key="comment.id" class="comment-item">
                <div class="comment-header">
                  <img :src="userAvatar" alt="Avatar" style="width: 24px; height: 24px; border-radius: 50%; margin-right: 8px;">
                  <strong>{{ comment.username }}</strong>
                  <span class="comment-votes">{{ comment.vote_count || comment.votes }} votos</span>
                  <button v-if="comment.user_id === currentUserId" @click="deleteComment(comment.id)" style="margin-left: 10px; background: none; border: none; color: #dc3545; cursor: pointer;">🗑️</button>
                </div>
                <p class="comment-text">{{ comment.content }}</p>
                <div style="margin-left: 32px; margin-top: 5px;">
                  <button @click="voteComment(comment.id, 'up')" :class="{ voted: commentUserVotes[comment.id] === 'up' }" style="background: none; border: none; cursor: pointer; color: #888;">▲</button>
                  <span style="margin: 0 5px;">{{ comment.vote_count || comment.votes }}</span>
                  <button @click="voteComment(comment.id, 'down')" :class="{ voted: commentUserVotes[comment.id] === 'down' }" style="background: none; border: none; cursor: pointer; color: #888;">▼</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ForumView',
  data() {
    return {
      currentFilter: 'home',
      selectedCategory: null,
      newPostTitle: '',
      newPostText: '',
      newPostCategory: '',
      modalOpen: false,
      selectedPostId: null,
      savedPosts: new Set(),
      newComment: '',
      commentInput: null,
      currentUserId: null,
      userAvatar: 'https://ddragon.leagueoflegends.com/cdn/14.1.1/img/profileicon/1.png',
      userVotes: {},              // { postId: 'up'|'down' }
      commentUserVotes: {},       // { commentId: 'up'|'down' }
      postComments: [],
      posts: [],
      loading: true,
      defaultPosts: []
    };
  },
  computed: {
    filteredPosts() {
      // Filtra las publicaciones según la categoría elegida
      let result = this.posts;
      if (this.currentFilter === 'saved') {
        result = this.posts.filter(p => this.savedPosts.has(p.id));
      }
      if (this.selectedCategory && this.currentFilter !== 'saved') {
        result = result.filter(p => p.tag === this.selectedCategory);
      }
      return result;
    },
    selectedPost() {
      // Publicación que se está viendo en la ventana de detalle
      return this.posts.find(p => p.id === this.selectedPostId);
    }
  },
  created() {
    const user = localStorage.getItem('nexus_user');
    if (user) {
      const userData = JSON.parse(user);
      this.currentUserId = userData.id;
      this.userAvatar = userData.avatar || this.userAvatar;
    }
    this.loadPosts();
    this.loadSavedPosts();
  },
  methods: {
    // Trae las publicaciones desde el servidor
    async loadPosts() {
      try {
        const response = await fetch('api/posts.php');
        const data = await response.json();
        if (data.success) {
          this.posts = data.posts;
        }
      } catch (e) {
        console.error('Error loading posts:', e);
      }
      this.loading = false;
    },
    // Trae las publicaciones guardadas por el usuario
    async loadSavedPosts() {
      if (!this.currentUserId) return;
      try {
        const response = await fetch('api/get_saved.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ user_id: this.currentUserId })
        });
        const data = await response.json();
        if (data.success) {
          data.posts.forEach(p => this.savedPosts.add(p.id));
        }
      } catch (e) {
        console.error('Error loading saved posts:', e);
      }
    },
    showSaved() {
      this.currentFilter = 'saved';
      this.selectedCategory = null;
    },
    // Cambia la fecha a un texto como "hace 5 min"
    formatTime(timestamp) {
      if (!timestamp) return '';
      const date = new Date(timestamp);
      const diff = Date.now() - date.getTime();
      const minutes = Math.floor(diff / 60000);
      const hours = Math.floor(diff / 3600000);
      const days = Math.floor(diff / 86400000);
      if (minutes < 1) return 'ahora';
      if (minutes < 60) return `hace ${minutes} min`;
      if (hours < 24) return `hace ${hours} horas`;
      return `hace ${days} días`;
    },
    // Vota una publicación (arriba o abajo)
    async vote(postId, direction) {
      try {
        const response = await fetch('api/comments.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            action: 'vote',
            post_id: postId,
            user_id: this.currentUserId,
            vote_type: direction
          })
        });
        const data = await response.json();
        if (data.success) {
          const post = this.posts.find(p => p.id === postId);
          if (post) post.vote_count = data.votes;

          const currentVote = this.userVotes[postId];
          if (currentVote === direction) {
            delete this.userVotes[postId];
          } else {
            this.userVotes[postId] = direction;
          }
          this.$forceUpdate();
        }
      } catch (e) {
        console.error('Error voting:', e);
      }
    },
    // Vota un comentario (arriba o abajo)
    async voteComment(commentId, direction) {
      try {
        const response = await fetch('api/comments.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            action: 'vote_comment',
            comment_id: commentId,
            user_id: this.currentUserId,
            vote_type: direction
          })
        });
        const data = await response.json();
        if (data.success) {
          const comment = this.postComments.find(c => c.id === commentId);
          if (comment) comment.vote_count = data.votes;

          const currentVote = this.commentUserVotes[commentId];
          if (currentVote === direction) {
            delete this.commentUserVotes[commentId];
          } else {
            this.commentUserVotes[commentId] = direction;
          }
          this.$forceUpdate();
        }
      } catch (e) {
        console.error('Error voting comment:', e);
      }
    },
    // Abre la ventana de detalle de una publicación y carga sus comentarios
    async openModal(postId) {
      this.selectedPostId = postId;
      this.modalOpen = true;
      document.body.style.overflow = 'hidden';

      try {
        const response = await fetch('api/comments.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            action: 'get',
            post_id: postId
          })
        });
        const data = await response.json();
        if (data.success) {
          this.postComments = data.comments;
        }
      } catch (e) {
        console.error('Error loading comments:', e);
      }
    },
    closeModal() {
      this.modalOpen = false;
      document.body.style.overflow = 'auto';
      this.selectedPostId = null;
      this.postComments = [];
    },
    // Guarda o quita una publicación de favoritos
    async toggleSave(postId) {
      try {
        const response = await fetch('api/saved.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            user_id: this.currentUserId,
            post_id: postId
          })
        });
        const data = await response.json();
        if (data.saved) {
          this.savedPosts.add(postId);
        } else {
          this.savedPosts.delete(postId);
        }
        this.$forceUpdate();
      } catch (e) {
        console.error('Error toggling save:', e);
      }
    },
    sharePost(post) {
      navigator.clipboard.writeText(`Nexus Hub - ${post.title}`);
      alert(`Compartir: "${post.title}"\n\n✓ Enlace copiado`);
    },
    // Borra una publicación (solo el dueño puede)
    async deletePost(postId) {
      if (confirm('¿Eliminar este post?')) {
        try {
          const response = await fetch('api/posts.php', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              id: postId,
              user_id: this.currentUserId
            })
          });
          const data = await response.json();
          if (data.success) {
            this.posts = this.posts.filter(p => p.id !== postId);
            if (this.selectedPostId === postId) this.closeModal();
          }
        } catch (e) {
          console.error('Error deleting post:', e);
        }
      }
    },
    // Borra un comentario (solo el dueño puede)
    async deleteComment(commentId) {
      try {
        const response = await fetch('api/comments.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            action: 'delete',
            comment_id: commentId,
            user_id: this.currentUserId
          })
        });
        const data = await response.json();
        if (data.success) {
          this.postComments = this.postComments.filter(c => c.id !== commentId);
          const post = this.posts.find(p => p.id === this.selectedPostId);
          if (post) post.comment_count = (post.comment_count || 0) - 1;
        }
      } catch (e) {
        console.error('Error deleting comment:', e);
      }
    },
    focusCommentInput() {
      this.$nextTick(() => {
        const input = this.$el.querySelector('.comment-input');
        if (input) input.focus();
      });
    },
    // Agrega un comentario a la publicación actual
    async addComment() {
      if (this.newComment.trim() && this.selectedPostId) {
        try {
          const response = await fetch('api/comments.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              action: 'add',
              post_id: this.selectedPostId,
              user_id: this.currentUserId,
              content: this.newComment
            })
          });
          const data = await response.json();
          if (data.success) {
            this.postComments.unshift({
              id: data.comment_id,
              user_id: this.currentUserId,
              username: localStorage.getItem('nexus_user') ? JSON.parse(localStorage.getItem('nexus_user')).username : 'Tú',
              content: this.newComment,
              votes: 0,
              vote_count: 0
            });
            const post = this.posts.find(p => p.id === this.selectedPostId);
            if (post) post.comment_count = (post.comment_count || 0) + 1;
            this.newComment = '';
          }
        } catch (e) {
          console.error('Error adding comment:', e);
        }
      }
    },
    // Crea una publicación nueva en el foro
    async createPost() {
      if (this.newPostTitle.trim() && this.newPostText.trim() && this.newPostCategory) {
        try {
          const response = await fetch('api/posts.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
              user_id: this.currentUserId,
              title: this.newPostTitle,
              description: this.newPostText,
              tag: this.newPostCategory,
              image: this.userAvatar
            })
          });
          const data = await response.json();
          if (data.success) {
            this.loadPosts();
            this.newPostTitle = '';
            this.newPostText = '';
            this.newPostCategory = '';
            this.userVotes[data.post_id] = 'up';
          }
        } catch (e) {
          console.error('Error creating post:', e);
        }
      } else {
        alert('Completa título, contenido y categoría');
      }
    }
  },
  mounted() {
    // Cierra la ventana de detalle con la tecla Escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') this.closeModal();
    });
  }
};
</script>
