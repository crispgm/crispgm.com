	#include <ngx_config.h>  
	#include <ngx_core.h>  
	#include <ngx_http.h>

	static char *ngx_http_crisp_hello_conf(ngx_conf_t *cf, ngx_command_t *cmd, void *conf);  
	static ngx_int_t ngx_http_crisp_hello_handler(ngx_http_request_t *r);

	static ngx_command_t ngx_http_crisp_hello_commands[] = {  
	  {  
	    ngx_string("crisp_hello"),  
	    NGX_HTTP_MAIN_CONF | NGX_HTTP_SRV_CONF | NGX_HTTP_LOC_CONF | NGX_HTTP_LMT_CONF | NGX_CONF_NOARGS,  
	    ngx_http_crisp_hello_conf,  
	    NGX_HTTP_LOC_CONF_OFFSET,  
	    NULL  
	  },  
	  ngx_null_command  
	};
