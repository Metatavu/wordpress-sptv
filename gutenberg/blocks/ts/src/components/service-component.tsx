import React from 'react';
import { wp } from 'wp';
import ServiceInspectorControls from './service-inspector-controls';

declare var wp: wp;
const { __ } = wp.i18n;

/**
 * Interface describing component props
 */
interface Props {
  editing: boolean,
  serviceId?: string,
  component?: string,
  language: string,
  onComponentChange(component: string) : void,
  onLanguageChange(language: string) : void,
  onServiceIdChange(serviceId: string): void
}

/**
 * Interface describing component state
 */
interface State {
}

/**
 * Service block
 */
class ServiceComponent extends React.Component<Props, State> {

  /*
   * Constructor
   * 
   * @param props props
   */
  constructor(props: Props) {
    super(props);
    this.state = { };
  }

  /**
   * Component render method
   */
  public render() {
    return (
      <div>
        { this.renderPreview() }
        { this.renderInspectorControls() }
      </div>
    );
  }

  /**
   * Renders inspector controls
   */
  private renderInspectorControls = () => {
    return (
      <ServiceInspectorControls
        editing={ this.props.editing } 
        serviceId={ this.props.serviceId }
        language={ this.props.language } 
        component={ this.props.component }
        onComponentChange={ this.props.onComponentChange }
        onLanguageChange={ this.props.onLanguageChange }
        onServiceIdChange={ this.props.onServiceIdChange }/>
    );
  }

  /**
   * Renders preview
   */
  private renderPreview = () => {
    return (
      <div>
        <wp.serverSideRender 
          block="sptv/service-block" 
          attributes={{
            id: this.props.serviceId, 
            language: this.props.language,
            component: this.props.component
          }}/>
      </div>
    );
  }

}
export default ServiceComponent;