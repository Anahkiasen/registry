interface ClassList {
	contains(classes: string): boolean;
	remove(classes: string);
	add(classes: string);
}

interface HTMLElement {
	getContext;
}

interface Element {
	classList: ClassList;
	dataset;
	value;
}

interface EventTarget {
	classList: ClassList;
	innerHTML;
}

interface Node {
	outerHTML;
}

declare var Chart: any;