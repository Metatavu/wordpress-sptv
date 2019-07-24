import React from 'react';
import { wp } from 'wp';

declare var wp: wp;

/**
 * Interface describing search input component props
 */
interface Props {
  style: object,
  inputLabel: string,
  inputHelp: string,
  doSearch(text: string): Promise<any[]>,
  getDisplayName(entity: any): string, 
  onSelect: (entity: any) => void
}

/**
 * Interface describing search input component state
 */
interface State {
  entities: any[], 
  searching: boolean,
  hoverIndex: number,
  pendingSearch: string | null
}

/**
 * Search input component
 */
export class SearchInput extends React.Component<Props, State> {

  /**
   * Constructor
   * 
   * @param props props
   */
  constructor(props: Props) {
    super(props);

    this.state = {
      entities: [], 
      searching: false,
      hoverIndex: -1,
      pendingSearch: null
    };
  }

  /**
   * Component render method
   */
  public render() {
    return (
      <div style={ this.props.style }>
        <wp.components.BaseControl style={{ width: "100%" }} id="search" label={ this.props.inputLabel } help={ this.props.inputHelp }>
          <input id="search" style={{ width: "100%" }} onChange={ this.onInputChange }/>
        </wp.components.BaseControl>
        <div style={{ height: "300px", overflowY: "auto" }}>
        { 
          this.state.searching ? (<wp.components.Placeholder style={{ height: "300px" }}><wp.components.Spinner /></wp.components.Placeholder> ) : this.state.entities.map((entity, index) => {
            return <div 
              onMouseOver={ () => this.setState({ hoverIndex: index }) } 
              onClick = { () => this.props.onSelect(entity) }
              style={{ fontWeight: this.state.hoverIndex == index ? "bold": "normal", cursor: "pointer", paddingTop: "5px", paddingBottom: "5px" }} 
              key={entity.id}>{this.props.getDisplayName(entity)}</div>
          })
        }
        </div>
      </div>
    );
  }

  /**
   * Executes a search with given string
   */
  private search = async (value: string) => {
    this.setState({ 
      searching: true 
    });

    let result = await this.props.doSearch(value);

    if (this.state.pendingSearch) {
      const pendingSearch = this.state.pendingSearch;
      this.setState({ 
        pendingSearch: null
      }, () => {
        this.search(pendingSearch);
      });

      
      return;  
    }

    this.setState({ 
      searching: false, 
      entities: result,
    });
  }

  /**
   * Event handler for handling use input changes
   * 
   * @param event event
   */
  private onInputChange = async (event: React.FormEvent<HTMLInputElement>) => {
    const value = event.currentTarget.value;
    if (value.length < 3) {
      return;
    }

    if (this.state.searching) {
      this.setState({ 
        pendingSearch: value
      });
      return;
    }

    await this.search(value);
  }

}