import React from 'react';
import { wp } from 'wp';
import OrganizationInspectorControls from './organization-inspector-controls';

declare var wp: wp;
const { __ } = wp.i18n;

/**
 * Interface describing component props
 */
interface Props {
  editing: boolean,
  organizationId?: string,
  component?: string,
  language: string,
  onComponentChange(component: string) : void,
  onLanguageChange(language: string) : void,
  onOrganizationIdChange(organizationId: string): void
}

/**
 * Interface describing component state
 */
interface State {
}

/**
 * Service block
 */
class OrganizationComponent extends React.Component<Props, State> {

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
      <OrganizationInspectorControls
        editing={ this.props.editing } 
        organizationId={ this.props.organizationId }
        language={ this.props.language } 
        component={ this.props.component }
        onComponentChange={ this.props.onComponentChange }
        onLanguageChange={ this.props.onLanguageChange }
        onOrganizationIdChange={ this.props.onOrganizationIdChange }/>
    );
  }

  /**
   * Renders preview
   */
  private renderPreview = () => {
    return (
      <div>
        <wp.components.ServerSideRender 
          block="sptv/organization-block" 
          attributes={{
            id: this.props.organizationId, 
            language: this.props.language,
            component: this.props.component
          }}/>
      </div>
    );
  }

}
export default OrganizationComponent;