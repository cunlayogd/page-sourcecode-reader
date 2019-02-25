<template>
  <div class="container-fluid mt-5 py-5">
    <div class="row py-5 justify-content-center">
      <div class="col-md-6">
        <div class="title m-b-md text-center my-5">
          <h1 text-success>Fetch HTML OF A PAGE</h1>
        </div>

        <form method="POST" novalidate>
          <div class="input-group input-group-lg mb-3">
            <input type="url" v-model="page_url" class="form-control" placeholder="E.g http://fayamedia.com" required>
            <div class="input-group-append">
              <button @click="processForm" class="btn btn-success" type="submit">
                <span v-if="loadingIcon" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                <span v-if="loadingIcon" class="sr-only">Loading...</span>
                Get Summary
              </button>
            </div>
          </div>
        </form>
        <div v-if="error" class="alert alert-warning" role="alert">
          <strong>Holy guacamole!</strong> {{ error }}
        </div>
      </div>
    </div>
    <div class="row mt-3 justify-content-center" v-if="source_code && summaries">
      <div class="col-md-6">
        <div class="my-5 mx-3">
            <div class="card">
              <div class="card-header">
                <h4>HTML source code</h4>
              </div>
              <div class="card-body bg-white">
                <div v-html="source_code" style="display: block; white-space: pre-wrap;"></div>
              </div>
            </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="text-center my-5">
            <h4 class="text-white">Document Summary</h4>
            <div class="table-responsive">
              <table class="table table-bordered bg-white">
                <thead>
                  <tr>
                    <th scope="col">Tag</th>
                    <th scope="col">Total Appearances</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="summary in summaries">
                    <td>
                      <button role="button" @click="highlight(summary.tag)" class="btn btn-link">{{ summary.tag }}</button>
                      </td>
                    <td>{{ summary.count }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
  .highlightText {
      background: #FF9800;
  }
</style>

<script>
  export default {
    data() {
      return {
          error: "",
          loadingIcon: false,
          page_url: '',
          source_code: "",
          summaries: []
      }
    },
    mounted() {
      // console.log('Component mounted.');
    },
    methods: {
      processForm(e) {
        // clear the source_code and summaries
        this.source_code = "";
        this.summaries = [];
        this.error = "";
        // prevent form default
        e.preventDefault();
        // validate request
        if (!this.page_url) {
          this.error = "Page URL is required";
        }
        else if (!this.validateURL(this.page_url)) {
          this.error = "Invalid URL. Sample url http://google.com";
        }
        else {
          // send ajax request with data
          this.loadingIcon = true;
          axios.post('/get-summary', {page_url: this.page_url})
            .then((res) => {
              this.loadingIcon = false;
              if (res.status == 200) {
                this.source_code = res.data.source_code;
                this.summaries = res.data.summary;
              }
            })
            .catch((error) => {
                this.loadingIcon = false;
                if (error.response.status == 500) {
                  if (error.response.data.error == true) {
                    this.error = error.response.data.message;
                  }
                }
                console.log(error.response);
            });
        }
      },
      highlight(tag) {
        if(!tag) {
            return this.source_code;
        }
        var pattern = "&lt;"+tag+"(?![\\w])";
        this.source_code = this.source_code.replace(new RegExp(pattern, "gi"), match => {
            return ('<span class=\'highlightText\'>' + match + '</span>');
        });
      },
      validateURL(url) {
        var pattern = /^(?:\w+:)?\/\/([^\s\.]+\.\S{2}|localhost[\:?\d]*)\S*$/;
        return pattern.test(url);
      }
    }
  }
</script>
