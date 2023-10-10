declare module "wp" {

  export interface WPElement {
    createElement(type: string|Function, props?: Object, children?: string|WPElement|WPElement[]): WPElement;
  }

  export interface WPTextControlProps {
    label?: string, 
    value?: string, 
    help?: string, 
    className?: string, 
    instanceId?: string, 
    onChange: (value: string) => void,
  }

  export interface WPSelectControlOption {
    label: string, 
    value: string
  }

  export interface WPSelectControlProps {
    label?: string, 
    value?: string, 
    help?: string, 
    className?: string, 
    instanceId?: string, 
    multiple?: boolean,
    onChange: (value: string | string[]) => void,
    options: WPSelectControlOption[]
  }

  export interface WPCheckboxControlProps {
    heading?: string,
		label?: string,
		help?: string,
		checked?: boolean
		onChange: (isChecked: boolean) => void
  }

  export interface WPTooltipProps {
    text: string
  }

  export interface WPCompose {
    withState: any;
  }

  export interface WPData {
    subscribe(callback : () => void): () => void,
    select(storeName: string): any;
    dispatch(storeName: string): any;
    registerStore(storeName: string, props: any): any;
    withSelect(mapSelectToProps: (select: any, ownProps: any) => void): (component: any) => any;
  }

  export interface WPHooks {
    addAction(hookName: string, namespace: string, callback: () => void, priority?: number): void;
  }

  export interface WPI18n {
    __: any;
    sprintf: any;
  }
  
  export interface WPBlockTypeEditParams {
    isSelected: boolean,
    attributes: any,
    setAttributes: (attributes: any) => void
  }
  
  export interface wp {
    data: WPData;
    hooks: WPHooks;
    element: WPElement;
    blocks: typeof import("@wordpress/blocks");
    editor: typeof import("@wordpress/editor");
    blockEditor: typeof import("@wordpress/block-editor");
    components: typeof import("@wordpress/components"); 
    compose: WPCompose;
    i18n: WPI18n;
    apiFetch: any;
    serverSideRender: any,
  }

}